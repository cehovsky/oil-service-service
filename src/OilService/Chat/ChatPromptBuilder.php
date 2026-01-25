<?php

declare(strict_types=1);

namespace App\OilService\Chat;

use App\OilService\DBAL\Entity\PriceListItem;
use App\OilService\DBAL\Enum\ChatKnowledgeItemTypeEnum;
use App\OilService\DBAL\Repository\ChatKnowledgeItemRepository;
use App\OilService\DBAL\Repository\PriceListItemRepository;
use App\OilService\Term\TermAvailabilityPolicy;

class ChatPromptBuilder
{
    private const string DEFAULT_LANGUAGE = 'cs-CZ';

    public function __construct(
        private readonly ChatKnowledgeItemRepository $chatKnowledgeItemRepository,
        private readonly PriceListItemRepository $priceListItemRepository,
        private readonly TermAvailabilityPolicy $termAvailabilityPolicy,
    ) {
    }

    public function buildSystemPrompt(string $language): string
    {
        $resolvedLanguage = $language !== '' ? $language : self::DEFAULT_LANGUAGE;

        $knowledgeItems = $this->chatKnowledgeItemRepository->findActiveKnowledgeByLanguage($resolvedLanguage);

        if ($knowledgeItems === []) {
            $knowledgeItems = $this->chatKnowledgeItemRepository->findActiveKnowledgeByLanguage(self::DEFAULT_LANGUAGE);
        }

        $services = $this->priceListItemRepository->findActivePublicItemsOrderedByLabel();

        $defaultServices = array_filter(
            $services,
            static fn (PriceListItem $item): bool => $item->getIsDefault(),
        );

        $addonServices = array_filter(
            $services,
            static fn (PriceListItem $item): bool => !$item->getIsDefault(),
        );

        $defaultServiceLines = array_map(
            static fn (PriceListItem $item): string => sprintf(
                '- %s (code: %s, %s Kč vč. DPH)',
                $item->getLabel(),
                $item->getCode(),
                $item->getPriceVat(),
            ),
            $defaultServices,
        );

        $addonServiceLines = array_map(
            static fn (PriceListItem $item): string => sprintf(
                '- %s (code: %s, %s Kč vč. DPH)',
                $item->getLabel(),
                $item->getCode(),
                $item->getPriceVat(),
            ),
            $addonServices,
        );

        $knowledgeLines = array_map(
            static fn ($item): string => sprintf('- %s: %s', $item->getName(), $item->getContent()),
            $knowledgeItems,
        );

        $today = new \DateTimeImmutable('today');
        $tomorrow = $today->modify('+1 day');
        $dayAfterTomorrow = $today->modify('+2 day');
        $timezone = date_default_timezone_get();
        $minimumAvailableDate = $this->termAvailabilityPolicy->getMinimumAvailableDate();
        $minimumDaysAhead = TermAvailabilityPolicy::MIN_DAYS_AHEAD;

        $persona = 'Jsi zkušený automechanik, který v češtině řeší objednávky mobilní výměny oleje a filtrů u zákazníků doma.';
        $languageRule = sprintf('Odpovídej vždy stručně a srozumitelně. Respektuj nastavený jazyk: %s.', $resolvedLanguage);
        $dateRule = sprintf(
            'Aktuální datum je %s (%s). Zítra je %s a pozítří %s. Relativní termíny vyhodnocuj podle tohoto data.',
            $today->format('Y-m-d'),
            $timezone,
            $tomorrow->format('Y-m-d'),
            $dayAfterTomorrow->format('Y-m-d'),
        );
        $orderGoal = 'Tvým cílem je vyplnit objednávku: jméno, telefon, e-mail, model auta, SPZ, adresa, preferovaný termín a čas. Dále můžeš nepovinně uložit poznámku.';
        $unknownRule = 'Pokud ti chybí data, ptej se na ně. Když nemůžeš odpovědět, řekni to narovinu a zapiš poznámku pro operátora.';
        $flowRule = 'Když odpovíš na dotaz z doplňujících informací, plynule a profesionálně navazuj na založení objednávky a zeptej se na chybějící údaj.';
        $termRule = sprintf(
            'Preferovaný termín a čas vybírej pouze z dostupných termínů (aktivní, nejdříve za %d dny, volná kapacita). Nikdy nenabízej dnešní nebo zítřejší datum. Nejbližší možný termín je %s. Pokud navrhuješ termíny, použij nástroj list_available_terms a drž se stejné logiky jako /oil-service/terms/available. Pokud uživatel žádá den nebo slot, který v dostupných termínech neexistuje, jasně řekni, že je termín plný nebo den nedostupný, a nabídni nejbližší dostupné termíny.',
            $minimumDaysAhead,
            $minimumAvailableDate->format('Y-m-d'),
        );
        $completionRule = 'Objednávku ukládej až po získání všech povinných údajů včetně data a časového slotu. Po úspěšném uložení objednávky odpověz finálním potvrzením, které musí obsahovat větu „Objednávka byla založena a obsluha vás bude kontaktovat.“ a zavolej complete_session.';
        $finishRule = 'Pokud objednávka ještě není uložená, vždy odpověď zakonči otázkou na chybějící údaje. Pokud je objednávka uložená, odpověz finálně bez další otázky.';
        $defaultServicesBlock = $defaultServiceLines === []
            ? ''
            : "Základní služby (vždy součástí objednávky):\n" . implode("\n", $defaultServiceLines);
        $addonServicesBlock = $addonServiceLines === []
            ? ''
            : "Doplňkové služby (nabízej jen pokud dává smysl; pro výběr používej kód nebo ID):\n" . implode("\n", $addonServiceLines);
        $knowledgeBlock = $knowledgeLines === [] ? '' : "Doplňující informace k službě:\n" . implode("\n", $knowledgeLines);

        $systemPromptParts = [
            $persona,
            $languageRule,
            $dateRule,
            $orderGoal,
            $unknownRule,
            $flowRule,
            $termRule,
            $completionRule,
            $finishRule,
            $defaultServicesBlock,
            $addonServicesBlock,
            $knowledgeBlock,
        ];

        return implode("\n\n", array_filter($systemPromptParts));
    }

    public function resolveGreeting(string $language): ?string
    {
        $resolvedLanguage = $language !== '' ? $language : self::DEFAULT_LANGUAGE;
        $item = $this->chatKnowledgeItemRepository->findActiveGreetingByLanguage($resolvedLanguage);

        if ($item === null) {
            $item = $this->chatKnowledgeItemRepository->findActiveGreetingByLanguage(self::DEFAULT_LANGUAGE);
        }

        return $item?->getContent();
    }
}
