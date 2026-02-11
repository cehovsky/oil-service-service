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
    private const string FALLBACK_GREETING = 'Dobrý den, mohu vám pomoci s výměnou oleje a filtrů?';

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
                '- %s (%s Kč vč. DPH)',
                $item->getLabel(),
                $item->getPriceVat(),
            ),
            $defaultServices,
        );

        $addonServiceLines = array_map(
            static fn (PriceListItem $item): string => sprintf(
                '- %s (%s Kč vč. DPH)',
                $item->getLabel(),
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

        $persona = 'Jsi profesionální a přátelský servisní technik mobilní výměny oleje. Komunikuješ stručně, jasně a přirozeně. Klade důraz na spokojenost zákazníka.';
        $languageRule = sprintf('Odpovídej vždy stručně a srozumitelně. Respektuj nastavený jazyk: %s.', $resolvedLanguage);
        $dateRule = sprintf(
            'Aktuální datum je %s (%s). Zítra je %s a pozítří %s. Relativní termíny vyhodnocuj podle tohoto data.',
            $today->format('Y-m-d'),
            $timezone,
            $tomorrow->format('Y-m-d'),
            $dayAfterTomorrow->format('Y-m-d'),
        );
        $orderGoal = 'Tvým cílem je postupně získat údaje pro objednávku: jméno, telefon, email, model auta, SPZ, adresa, preferovaný termín a časový slot. VIN je nepovinné. Ptej se přirozeně a postupně, ne všechno najednou.';
        $unknownRule = 'Pokud ti chybí data, zeptej se postupně na 1-2 věci najednou, ne na všechno. Když nemůžeš odpovědět, řekni to narovinu a zapiš poznámku pro operátora.';
        $flowRule = 'Komunikuj přirozeně a plynule. Nejprve se zeptej na základní kontakt (jméno, telefon, email), pak na auto (model, SPZ; VIN je nepovinný), pak na adresu a nakonec na termín. Nepiš dlouhé seznamy všech požadovaných údajů najednou.';
        $termRule = sprintf(
            'Preferovaný termín a čas vybírej pouze z dostupných termínů (aktivní, nejdříve za %d dny, volná kapacita). Nikdy nenabízej dnešní nebo zítřejší datum. Nejbližší možný termín je %s. Při navrhování termínů zavolej list_available_terms a uveď 2-3 nejbližší volné dny s časovými sloty. Pokud uživatel žádá obsazený termín, řekni mu to a nabídni nejbližší dostupné alternativy.',
            $minimumDaysAhead,
            $minimumAvailableDate->format('Y-m-d'),
        );
        $completionRule = 'Objednávku uložíš pomocí submit_order až po získání VŠECH povinných údajů: jméno, telefon, email, model auta, SPZ, adresa, realizationDate, realizationTimeSlot. VIN je nepovinné a nesmí blokovat vytvoření objednávky. Po úspěšném uložení objednávky NEZAVÍREJ session automaticky - místo toho zákazníkovi profesionálně potvrď objednávku a NABÍDNI další služby z ceníku. Teprve když zákazník odmítne nebo se rozloučí, zavolej complete_session.';
        $finishRule = 'Pokud objednávka ještě není uložená, vždy se zeptej na další chybějící údaje. Po uložení objednávky nabídni další služby z doplňkových služeb. Teprve po rozloučení nebo odmítnutí dalších služeb zavolej complete_session a rozluč se.';
        $noForcedOilChangeRule = 'Nikdy nevnucuj výměnu oleje vlastním olejem, pokud se na to zákazník sám nezeptá.';
        $productSuggestionRule = 'Po úspěšném uložení objednávky (po zavolání submit_order) VŽDY nabídni zákazníkovi další služby z doplňkových služeb. Nabízej je přirozeně: "Objednávka je založena! Mohu vám ještě nabídnout..." a pak vypiš 2-3 relevantní služby s cenami. Služby nabízej podle názvu.';
        $locationPermissionRule = 'Během sbírání údajů se zeptej, zda bude výměna prováděna na pozemku zákazníka nebo zda má zákazník povolení k provedení výměny na daném místě. Upozorni, že výměna oleje na veřejné silnici není možná.';
        $oilAndVolumeRule = 'Pokud znáš typ oleje a objem náplně pro daný model auta, můžeš to zmínit pro informaci, ale nezpomaluj tím proces objednávky.';
        $additionalServiceOfferRule = 'Po úspěšném vytvoření objednávky (po submit_order) VŽDY aktivně nabídni doplňkové služby z ceníku. Pokud zákazník chce přidat další služby, řekni mu že je přidáš a zavolej submit_order ZNOVU se VŠEMI údaji VČETNĚ nových priceListItemIds (služby které zákazník chce přidat). Důležité: Každé volání submit_order PŘEPÍŠE předchozí objednávku, takže vždy musíš předat kompletní seznam všech služeb které zákazník chce. Po přidání služeb nebo jejich odmítnutí se rozluč a zavolej complete_session.';

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
            $noForcedOilChangeRule,
            $productSuggestionRule,
            $locationPermissionRule,
            $oilAndVolumeRule,
            $additionalServiceOfferRule,
            $defaultServicesBlock,
            $addonServicesBlock,
            $knowledgeBlock,
        ];

        return implode("\n\n", array_filter($systemPromptParts));
    }

    public function resolveGreeting(string $language): string
    {
        $resolvedLanguage = $language !== '' ? $language : self::DEFAULT_LANGUAGE;
        $item = $this->chatKnowledgeItemRepository->findActiveGreetingByLanguage($resolvedLanguage);

        if ($item === null) {
            $item = $this->chatKnowledgeItemRepository->findActiveGreetingByLanguage(self::DEFAULT_LANGUAGE);
        }

        return $item?->getContent() ?? self::FALLBACK_GREETING;
    }
}
