<?php

declare(strict_types=1);

namespace App\OilService\Chat;

use App\OilService\DBAL\Entity\PriceListItem;
use App\OilService\DBAL\Enum\ChatKnowledgeItemTypeEnum;
use App\OilService\DBAL\Repository\ChatKnowledgeItemRepository;
use App\OilService\DBAL\Repository\PriceListItemRepository;

class ChatPromptBuilder
{
    private const string DEFAULT_LANGUAGE = 'cs-CZ';

    public function __construct(
        private readonly ChatKnowledgeItemRepository $chatKnowledgeItemRepository,
        private readonly PriceListItemRepository $priceListItemRepository,
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

        $serviceLines = array_map(
            static fn (PriceListItem $item): string => sprintf('- %s (%s Kč vč. DPH)', $item->getLabel(), $item->getPriceVat()),
            $services,
        );

        $knowledgeLines = array_map(
            static fn ($item): string => sprintf('- %s: %s', $item->getName(), $item->getContent()),
            $knowledgeItems,
        );

        $persona = 'Jsi zkušený automechanik, který v češtině řeší objednávky mobilní výměny oleje a filtrů u zákazníků doma.';
        $languageRule = sprintf('Odpovídej vždy stručně, srozumitelně a česky. Respektuj nastavený jazyk: %s.', $resolvedLanguage);
        $orderGoal = 'Tvým cílem je vyplnit objednávku: jméno, telefon, e-mail, model auta, SPZ, adresa, preferovaný termín a čas, výběr služeb, poznámka. Vše ukládej průběžně.';
        $unknownRule = 'Pokud ti chybí data, ptej se na ně. Když nemůžeš odpovědět, řekni to narovinu a zapiš poznámku pro operátora.';
        $servicesBlock = $serviceLines === [] ? '' : "Nabízené služby (používej při doporučení):\n" . implode("\n", $serviceLines);
        $knowledgeBlock = $knowledgeLines === [] ? '' : "Doplňující informace k službě:\n" . implode("\n", $knowledgeLines);

        $systemPromptParts = [
            $persona,
            $languageRule,
            $orderGoal,
            $unknownRule,
            $servicesBlock,
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
