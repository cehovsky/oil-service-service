<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Exception\InvalidArgumentException;
use ArrayIterator;
use JsonSerializable;
use Countable;
use IteratorAggregate;
use ArrayAccess;
use Traversable;

/**
 * @template-implements ArrayAccess<string|int, mixed>
 * @template-implements IteratorAggregate<string|int, mixed>
 */
abstract class ArrayCollection implements Countable, IteratorAggregate, ArrayAccess, JsonSerializable
{
    /** @var array<string|int, mixed> */
    protected array $items = [];

    /**
     * @param array<string|int, mixed> $items
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function onBeforeSet(mixed $value): void
    {
        $this->assertItemType($value);
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function assertItemType(mixed $value): void
    {
        $class = $this->getItemClass();
        if (!$value instanceof $class) {
            throw new InvalidArgumentException(
                sprintf(
                    "Invalid item type encountered: '%s' expected '%s'.",
                    (is_object($value) ? get_class($value) : gettype($value)),
                    $class
                )
            );
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function onBeforeAdd(mixed $value): void
    {
        $this->assertItemType($value);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function add(mixed $value): void
    {
        $this->onBeforeAdd($value);
        $this->items[] = $value;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function set(string | int $key, mixed $value): void
    {
        $this->onBeforeSet($value);
        $this->items[$key] = $value;
    }

    public function remove(string | int $key): mixed
    {
        if (!$this->keyExists($key)) {
            return null;
        }
        $removed = $this->get($key);
        unset($this->items[$key]);

        return $removed;
    }

    public function get(string | int $key): mixed
    {
        if (!$this->keyExists($key)) {
            return null;
        }

        return $this->items[$key];
    }

    public function keyExists(string | int $key): bool
    {
        if (!array_key_exists($key, $this->items)) {
            return false;
        }

        return true;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return ($this->items[$offset] ?? null);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->add($value);

            return;
        }

        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return array<string|int, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->items;
    }

    public function first(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }
        $itemsCopy = array_values($this->items); // Copy array values to preserve the internal array pointer

        return reset($itemsCopy);
    }

    public function last(): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }
        $itemsCopy = array_values($this->items); // Copy array values to preserve the internal array pointer

        return end($itemsCopy);
    }

    /**
     * @return array<string|int, mixed>
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Return class of an Item
     *
     * @return class-string|string
     */
    abstract public function getItemClass(): string;
}
