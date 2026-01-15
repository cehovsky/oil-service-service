<?php

declare(strict_types=1);

namespace App\OilService;

use App\OilService\DBAL\Entity\PriceListItem;
use App\OilService\Factory\EntityFactory;
use Doctrine\ORM\EntityManagerInterface;

class PriceListItemService
{
    public const int DEFAULT_VAT = 21;

    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createPriceListItem(
        string $label,
        ?string $description,
        ?string $invoiceLabel,
        string $price,
        ?int $vat,
        bool $isActive,
        bool $isPublic,
        bool $isDefault,
        bool $isHiddenOnInvoice,
        string $code,
        ?string $brand,
        ?string $externalCode,
    ): PriceListItem {
        $vatValue = $vat ?? self::DEFAULT_VAT;
        $priceVat = $this->calculatePriceVat($price, $vatValue);

        $priceListItem = $this->entityFactory->createPriceListItem(
            $label,
            $description,
            $invoiceLabel,
            $price,
            $vatValue,
            $priceVat,
            $isActive,
            $isPublic,
            $isDefault,
            $isHiddenOnInvoice,
            $code,
            $brand,
            $externalCode,
        );

        $this->entityManager->persist($priceListItem);
        $this->entityManager->flush();

        return $priceListItem;
    }

    public function updatePriceListItem(
        PriceListItem $priceListItem,
        string $label,
        ?string $description,
        ?string $invoiceLabel,
        string $price,
        int $vat,
        bool $isActive,
        bool $isPublic,
        bool $isDefault,
        bool $isHiddenOnInvoice,
        string $code,
        ?string $brand,
        ?string $externalCode,
    ): PriceListItem {
        $priceVat = $this->calculatePriceVat($price, $vat);

        $priceListItem->setLabel($label);
        $priceListItem->setDescription($description);
        $priceListItem->setInvoiceLabel($invoiceLabel);
        $priceListItem->setPrice($price);
        $priceListItem->setVat($vat);
        $priceListItem->setPriceVat($priceVat);
        $priceListItem->setIsActive($isActive);
        $priceListItem->setIsPublic($isPublic);
        $priceListItem->setIsDefault($isDefault);
        $priceListItem->setIsHiddenOnInvoice($isHiddenOnInvoice);
        $priceListItem->setCode($code);
        $priceListItem->setBrand($brand);
        $priceListItem->setExternalCode($externalCode);

        $this->entityManager->flush();

        return $priceListItem;
    }

    public function deletePriceListItem(PriceListItem $priceListItem): void
    {
        $this->entityManager->remove($priceListItem);
        $this->entityManager->flush();
    }

    private function calculatePriceVat(string $price, int $vat): string
    {
        $priceValue = (float) $price;
        $vatMultiplier = 1 + ($vat / 100);
        $priceVat = round($priceValue * $vatMultiplier, 2);

        return number_format($priceVat, 2, '.', '');
    }
}
