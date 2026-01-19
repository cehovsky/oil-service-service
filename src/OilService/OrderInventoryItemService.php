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
use App\OilService\DBAL\Repository\InventoryItemRepository;
use App\OilService\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class OrderInventoryItemService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly InventoryItemRepository $inventoryItemRepository,
    ) {
    }

    /**
     * @param array<int, array{inventoryItemId: string, quantity: int}> $items
     */
    public function updateOrderInventoryItems(Order $order, array $items, AuthUser $actor): Order
    {
        $requestedItems = $this->normalizeRequestedItems($items);

        $inventoryItems = $this->inventoryItemRepository->findByIds(array_keys($requestedItems));
        $inventoryItemsById = [];

        foreach ($inventoryItems as $inventoryItem) {
            $inventoryItemsById[$inventoryItem->getId()->__toString()] = $inventoryItem;
        }

        if (count($inventoryItemsById) !== count($requestedItems)) {
            throw $this->createInvalidInventoryItemsException();
        }

        $existingItems = [];

        foreach ($order->getOrderInventoryItems() as $orderInventoryItem) {
            $existingItems[$orderInventoryItem->getInventoryItem()->getId()->__toString()] = $orderInventoryItem;
        }

        $stockLevels = [];

        foreach ($inventoryItemsById as $inventoryItemId => $inventoryItem) {
            $stockLevels[$inventoryItemId] = $inventoryItem->getStockCount();
        }

        foreach ($existingItems as $inventoryItemId => $orderInventoryItem) {
            if (!isset($stockLevels[$inventoryItemId])) {
                $stockLevels[$inventoryItemId] = $orderInventoryItem->getInventoryItem()->getStockCount();
            }
        }

        $deltas = [];

        foreach ($requestedItems as $inventoryItemId => $quantity) {
            $previousQuantity = isset($existingItems[$inventoryItemId])
                ? $existingItems[$inventoryItemId]->getQuantity()
                : 0;
            $delta = $quantity - $previousQuantity;

            if ($delta === 0) {
                continue;
            }

            $deltas[$inventoryItemId] = $delta;
            $this->applyDeltaToStockLevel($stockLevels, $inventoryItemId, $delta);
        }

        foreach ($existingItems as $inventoryItemId => $orderInventoryItem) {
            if (!isset($requestedItems[$inventoryItemId])) {
                $delta = -$orderInventoryItem->getQuantity();
                $deltas[$inventoryItemId] = $delta;
                $this->applyDeltaToStockLevel($stockLevels, $inventoryItemId, $delta);
            }
        }

        foreach ($stockLevels as $inventoryItemId => $stockCount) {
            if ($stockCount < 0) {
                throw $this->createInvalidStockLevelException();
            }
        }

        foreach ($requestedItems as $inventoryItemId => $quantity) {
            $inventoryItem = $inventoryItemsById[$inventoryItemId];
            $existingOrderItem = $existingItems[$inventoryItemId] ?? null;

            if ($existingOrderItem !== null) {
                if ($existingOrderItem->getQuantity() === $quantity) {
                    continue;
                }

                $existingOrderItem->setQuantity($quantity);
                $existingOrderItem->setUpdatedAt(new DateTimeImmutable());
            } else {
                $orderInventoryItem = $this->entityFactory->createOrderInventoryItem(
                    $order,
                    $inventoryItem,
                    $quantity,
                );

                $order->addOrderInventoryItem($orderInventoryItem);
                $this->entityManager->persist($orderInventoryItem);
            }

            if (isset($deltas[$inventoryItemId])) {
                $this->createOrderInventoryHistory(
                    $inventoryItem,
                    $order,
                    $deltas[$inventoryItemId],
                    $actor,
                );
            }
        }

        foreach ($existingItems as $inventoryItemId => $orderInventoryItem) {
            if (isset($requestedItems[$inventoryItemId])) {
                continue;
            }

            $inventoryItem = $orderInventoryItem->getInventoryItem();

            if (isset($deltas[$inventoryItemId])) {
                $this->createOrderInventoryHistory(
                    $inventoryItem,
                    $order,
                    $deltas[$inventoryItemId],
                    $actor,
                );
            }

            $order->removeOrderInventoryItem($orderInventoryItem);
            $this->entityManager->remove($orderInventoryItem);
        }

        foreach ($deltas as $inventoryItemId => $delta) {
            $inventoryItem = $inventoryItemsById[$inventoryItemId] ?? $existingItems[$inventoryItemId]->getInventoryItem();
            $inventoryItem->setStockCount($stockLevels[$inventoryItemId]);
            $inventoryItem->setUpdatedBy($actor);
            $inventoryItem->setUpdatedAt(new DateTimeImmutable());
        }

        $this->entityManager->flush();

        return $order;
    }

    /**
     * @param array<int, array{inventoryItemId: string, quantity: int}> $items
     *
     * @return array<string, int>
     */
    private function normalizeRequestedItems(array $items): array
    {
        $normalized = [];

        foreach ($items as $item) {
            $inventoryItemId = $item['inventoryItemId'] ?? null;
            $quantity = $item['quantity'] ?? null;

            if (!is_string($inventoryItemId) || $inventoryItemId === '') {
                throw $this->createInvalidInventoryItemsException();
            }

            if (!is_int($quantity) && !is_numeric($quantity)) {
                throw $this->createInvalidQuantityException();
            }

            $quantityValue = (int) $quantity;

            if ($quantityValue <= 0) {
                throw $this->createInvalidQuantityException();
            }

            if (isset($normalized[$inventoryItemId])) {
                throw $this->createDuplicateInventoryItemsException();
            }

            $normalized[$inventoryItemId] = $quantityValue;
        }

        return $normalized;
    }

    private function applyDeltaToStockLevel(array &$stockLevels, string $inventoryItemId, int $delta): void
    {
        $stockLevels[$inventoryItemId] ??= 0;
        $stockLevels[$inventoryItemId] += -$delta;
    }

    private function createOrderInventoryHistory(
        InventoryItem $inventoryItem,
        Order $order,
        int $delta,
        AuthUser $actor,
    ): void {
        if ($delta === 0) {
            return;
        }

        $isIncrement = $delta < 0;
        $quantity = abs($delta);

        $history = $this->entityFactory->createInventoryItemHistory(
            $inventoryItem,
            InventoryMovementTypeEnum::ORDER_TRANSFER,
            $quantity,
            $isIncrement,
            $actor,
            $order,
            null,
            null,
            null,
            null,
        );

        $inventoryItem->addHistory($history);
        $this->entityManager->persist($history);
    }

    private function createInvalidInventoryItemsException(): ValidationException
    {
        $errorCollection = new ErrorCollection();
        $errorCollection->add(new ErrorItem(
            'Selected inventory items are not valid.',
            'invalidInventoryItems',
            null,
        ));

        return new ValidationException(errorCollection: $errorCollection);
    }

    private function createDuplicateInventoryItemsException(): ValidationException
    {
        $errorCollection = new ErrorCollection();
        $errorCollection->add(new ErrorItem(
            'Duplicate inventory item IDs are not allowed.',
            'duplicateInventoryItems',
            null,
        ));

        return new ValidationException(errorCollection: $errorCollection);
    }

    private function createInvalidQuantityException(): ValidationException
    {
        $errorCollection = new ErrorCollection();
        $errorCollection->add(new ErrorItem(
            'Quantity must be a positive number.',
            'invalidQuantity',
            null,
        ));

        return new ValidationException(errorCollection: $errorCollection);
    }

    private function createInvalidStockLevelException(): ValidationException
    {
        $errorCollection = new ErrorCollection();
        $errorCollection->add(new ErrorItem(
            'Stock level cannot be negative.',
            'invalidStockLevel',
            null,
        ));

        return new ValidationException(errorCollection: $errorCollection);
    }
}
