<?php

declare(strict_types=1);

namespace App\OilService;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Domain\Error\ErrorCollection;
use App\Domain\Error\ErrorItem;
use App\Domain\Exception\ValidationException;
use App\OilService\DBAL\Entity\InventoryItem;
use App\OilService\DBAL\Entity\Order;
use App\OilService\DBAL\Enum\InventoryMovementTypeEnum;
use App\OilService\DBAL\Repository\OrderRepository;
use App\OilService\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InventoryItemService
{
    public const int DEFAULT_VAT = 21;

    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orderRepository,
    ) {
    }

    public function createInventoryItem(
        string $label,
        ?string $description,
        string $code,
        ?string $externalCode,
        ?string $price,
        ?int $vat,
        AuthUser $actor,
    ): InventoryItem {
        $vatValue = null;
        $priceValue = null;
        $priceVatValue = null;

        if ($price !== null) {
            $priceValue = $this->normalizePrice($price);
            $vatValue = $vat;

            if ($vatValue !== null) {
                $priceVatValue = $this->calculatePriceVat($priceValue, $vatValue);
            }
        }

        $stockCount = 0;

        $inventoryItem = $this->entityFactory->createInventoryItem(
            $label,
            $description,
            $code,
            $externalCode,
            $priceValue,
            $vatValue,
            $priceVatValue,
            $stockCount,
            $actor,
            $actor,
        );

        $this->entityManager->persist($inventoryItem);
        $this->entityManager->flush();

        return $inventoryItem;
    }

    public function updateInventoryItem(
        InventoryItem $inventoryItem,
        string $label,
        ?string $description,
        string $code,
        ?string $externalCode,
        ?string $price,
        ?int $vat,
        AuthUser $actor,
    ): InventoryItem {
        $vatValue = null;
        $priceValue = null;
        $priceVatValue = null;

        if ($price !== null) {
            $priceValue = $this->normalizePrice($price);
            $vatValue = $vat;

            if ($vatValue !== null) {
                $priceVatValue = $this->calculatePriceVat($priceValue, $vatValue);
            }
        }

        $inventoryItem->setLabel($label);
        $inventoryItem->setDescription($description);
        $inventoryItem->setCode($code);
        $inventoryItem->setExternalCode($externalCode);
        $inventoryItem->setPrice($priceValue);
        $inventoryItem->setVat($vatValue);
        $inventoryItem->setPriceVat($priceVatValue);
        $inventoryItem->setUpdatedBy($actor);
        $inventoryItem->setUpdatedAt(new DateTimeImmutable());

        $this->entityManager->flush();

        return $inventoryItem;
    }

    public function deleteInventoryItem(InventoryItem $inventoryItem): void
    {
        $this->entityManager->remove($inventoryItem);
        $this->entityManager->flush();
    }

    public function stockIn(
        InventoryItem $inventoryItem,
        int $quantity,
        ?string $orderId,
        ?string $price,
        ?int $vat,
        ?string $note,
        AuthUser $actor,
    ): InventoryItem {
        if ($quantity <= 0) {
            throw $this->createInvalidQuantityException($quantity);
        }

        $order = $this->findOrder($orderId);

        $priceValue = null;
        $vatValue = null;
        $priceVatValue = null;

        if ($price !== null) {
            $vatValue = $vat ?? self::DEFAULT_VAT;
            $priceValue = $this->normalizePrice($price);
            $priceVatValue = $this->calculatePriceVat($priceValue, $vatValue);
        }

        $newCount = $inventoryItem->getStockCount() + $quantity;

        $history = $this->entityFactory->createInventoryItemHistory(
            $inventoryItem,
            InventoryMovementTypeEnum::STOCK_IN,
            $quantity,
            true,
            $actor,
            $order,
            $priceValue,
            $vatValue,
            $priceVatValue,
            $note,
        );

        $inventoryItem->addHistory($history);
        $inventoryItem->setStockCount($newCount);
        $inventoryItem->setUpdatedBy($actor);
        $inventoryItem->setUpdatedAt(new DateTimeImmutable());

        $this->entityManager->persist($history);
        $this->entityManager->flush();

        return $inventoryItem;
    }

    public function stockOut(
        InventoryItem $inventoryItem,
        int $quantity,
        ?string $orderId,
        ?string $note,
        AuthUser $actor,
    ): InventoryItem {
        if ($quantity <= 0) {
            throw $this->createInvalidQuantityException($quantity);
        }

        $order = $this->findOrder($orderId);
        $newCount = $inventoryItem->getStockCount() - $quantity;

        if ($newCount < 0) {
            throw $this->createInvalidStockLevelException($newCount);
        }

        $history = $this->entityFactory->createInventoryItemHistory(
            $inventoryItem,
            InventoryMovementTypeEnum::STOCK_OUT,
            $quantity,
            false,
            $actor,
            $order,
            null,
            null,
            null,
            $note,
        );

        $inventoryItem->addHistory($history);
        $inventoryItem->setStockCount($newCount);
        $inventoryItem->setUpdatedBy($actor);
        $inventoryItem->setUpdatedAt(new DateTimeImmutable());

        $this->entityManager->persist($history);
        $this->entityManager->flush();

        return $inventoryItem;
    }

    public function calculateStockCountFromHistory(InventoryItem $inventoryItem): int
    {
        $count = 0;

        foreach ($inventoryItem->getHistory() as $history) {
            $count += $history->getIsIncrement() ? $history->getQuantity() : -$history->getQuantity();
        }

        return $count;
    }

    private function findOrder(?string $orderId): ?Order
    {
        if ($orderId === null) {
            return null;
        }

        $order = $this->orderRepository->find($orderId);

        if ($order === null) {
            throw new NotFoundHttpException();
        }

        return $order;
    }

    private function createInvalidQuantityException(int $quantity): ValidationException
    {
        $errorCollection = new ErrorCollection();
        $errorCollection->add(new ErrorItem(
            'Quantity must be a positive number.',
            'invalidQuantity',
            (string) $quantity,
        ));

        return new ValidationException(errorCollection: $errorCollection);
    }

    private function createInvalidStockLevelException(int $stockCount): ValidationException
    {
        $errorCollection = new ErrorCollection();
        $errorCollection->add(new ErrorItem(
            'Stock level cannot be negative.',
            'invalidStockLevel',
            (string) $stockCount,
        ));

        return new ValidationException(errorCollection: $errorCollection);
    }

    private function calculatePriceVat(string $price, int $vat): string
    {
        $priceValue = (float) $price;
        $vatMultiplier = 1 + ($vat / 100);
        $priceVat = round($priceValue * $vatMultiplier, 2);

        return number_format($priceVat, 2, '.', '');
    }

    private function normalizePrice(string $price): string
    {
        return number_format((float) $price, 2, '.', '');
    }
}
