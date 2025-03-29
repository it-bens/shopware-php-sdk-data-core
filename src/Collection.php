<?php

declare(strict_types=1);

namespace ITB\ShopwareSdkDataCore;

/**
 * @phpstan-template T of object
 *
 * @phpstan-consistent-constructor
 *
 * @implements \IteratorAggregate<array-key, T>
 */
abstract class Collection extends Struct implements \Countable, \IteratorAggregate
{
    /**
     * @var array<array-key, T>
     */
    protected array $elements = [];

    /**
     * @param iterable<T> $elements
     */
    public function __construct(iterable $elements = [])
    {
        $elements = $elements instanceof \Traversable ? iterator_to_array($elements) : $elements;

        foreach ($elements as $key => $element) {
            $this->set($key, $element);
        }
    }

    /**
     * @api
     *
     * Adds a single element to the collection.
     * The type is validated against the expected class. An exception is thrown if the type does not match.
     *
     * @param T $element
     */
    public function add(mixed $element): void
    {
        $this->validateType($element);

        $this->elements[] = $element;
    }

    /**
     * @api
     *
     * Removes all elements from the collection.
     */
    public function clear(): void
    {
        $this->elements = [];
    }

    /**
     * @api
     *
     * Returns the number of elements in the collection.
     */
    public function count(): int
    {
        return \count($this->elements);
    }

    /**
     * @api
     *
     * Returns a new collection with the elements that pass the given closure.
     *
     * @return static<T>
     */
    public function filter(\Closure $closure): static
    {
        return new static(array_filter($this->elements, $closure));
    }

    /**
     * @api
     *
     * Returns the first element of the collection or null if the collection is empty.
     *
     * @return ?T
     */
    public function first(): mixed
    {
        return array_values($this->elements)[0] ?? null;
    }

    /**
     * @api
     *
     * Returns a mapped array of the elements in the collection.
     * Elements that don't pass the PHP empty method are filtered out.
     *
     * @template R
     *
     * @param \Closure(T): ?R $closure
     * @return array<array-key, R>
     */
    public function fmap(\Closure $closure): array
    {
        return array_filter($this->map($closure));
    }

    /**
     * @api
     *
     * Returns the element with the given key or null if the element does not exist.
     *
     * @return ?T
     */
    public function get(int|string $key): mixed
    {
        if ($this->has($key)) {
            return $this->elements[$key];
        }

        return null;
    }

    /**
     * @api
     *
     * Returns the elements of the collection.
     *
     * @return array<array-key, T>
     */
    public function getElements(): iterable
    {
        return $this->elements;
    }

    /**
     * @api
     *
     * Returns the expected class of the collection.
     *
     * @return class-string<T>
     */
    abstract public function getExpectedClass(): string;

    /**
     * @api
     *
     * Returns the iterator for the collection.
     *
     * @return \Generator<array-key, T>
     */
    public function getIterator(): \Generator
    {
        yield from $this->elements;
    }

    /**
     * @api
     *
     * Returns the keys of the collection.
     *
     * @return array<array-key>
     */
    public function getKeys(): array
    {
        return array_keys($this->elements);
    }

    /**
     * @api
     *
     * Checks if the collection has an element with the given key.
     */
    public function has(int|string $key): bool
    {
        return \array_key_exists($key, $this->elements);
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        /**
         * @param T $element
         * @return ?array
         */
        $callable = static function (mixed $element): ?array {
            if ($element instanceof Struct) {
                return $element->jsonSerialize();
            }

            return null;
        };

        return array_values(array_map($callable, $this->elements));
    }

    /**
     * @api
     *
     * Returns the last element of the collection or null if the collection is empty.
     *
     * @return ?T
     */
    public function last()
    {
        return array_values($this->elements)[\count($this->elements) - 1] ?? null;
    }

    /**
     * @api
     *
     * Returns a mapped array of the elements in the collection.
     *
     * @template R
     *
     * @param \Closure(T): ?R $closure
     * @return array<int|string, ?R>
     */
    public function map(\Closure $closure): array
    {
        return array_map($closure, $this->elements);
    }

    /**
     * @api
     *
     * Returns a reduction of the elements in the collection.
     *
     * @param \Closure(mixed, T): mixed $closure
     */
    public function reduce(\Closure $closure, mixed $initial = null): mixed
    {
        return array_reduce($this->elements, $closure, $initial);
    }

    /**
     * @api
     *
     * Removes the element with the given key from the collection.
     */
    public function remove(int|string $key): void
    {
        unset($this->elements[$key]);
    }

    /**
     * @api
     *
     * Sets the element with the given key in the collection.
     *
     * @param T $element
     */
    public function set(int|string|null $key, mixed $element): void
    {
        $this->validateType($element);

        if ($key === null) {
            $this->elements[] = $element;
            return;
        }

        $this->elements[$key] = $element;
    }

    /**
     * @api
     *
     * Returns a new collection with the elements after the given offset with the given length.
     * If the length is null, all elements after the offset are returned.
     * Keys are preserved.
     *
     * @return static<T>
     */
    public function slice(int $offset, ?int $length = null): static
    {
        return new static(\array_slice($this->elements, $offset, $length, true));
    }

    /**
     * @api
     *
     * Sorts the collection with the given closure.
     */
    public function sort(\Closure $closure): void
    {
        uasort($this->elements, $closure);
    }

    protected function validateType(object $element): void
    {
        $expectedClass = $this->getExpectedClass();
        if (! $element instanceof $expectedClass) {
            $elementClass = $element::class;

            throw new \InvalidArgumentException(
                sprintf('Expected collection element of type %s got %s', $expectedClass, $elementClass)
            );
        }
    }
}
