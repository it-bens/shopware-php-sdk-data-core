<?php

declare(strict_types=1);

namespace ITB\ShopwareSdkDataCore\Entity;

use ITB\ShopwareSdkDataCore\Collection;

/**
 * @phpstan-template E of Entity
 *
 * @extends Collection<Entity>
 */
abstract class EntityCollection extends Collection
{
    /**
     * @api
     *
     * Returns a new collection with the entities whose given property matches the given value.
     *
     * @return self<E>
     */
    public function filterByProperty(string $property, mixed $value): self
    {
        return $this->filter(static fn (Entity $struct): bool => $value === $struct->{$property});
    }

    /**
     * @api
     *
     * Returns all the ids of the entities in the collection.
     *
     * @return string[]
     */
    public function getIds(): array
    {
        return $this->fmap(static fn (Entity $entity): string => $entity->id);
    }

    /**
     * @api
     *
     * Inserts an entity at the given position in the collection.
     * The keys of the collection will be reset.
     *
     * @param E $entity
     */
    public function insert(int $position, Entity $entity): void
    {
        $items = array_values($this->elements);

        $this->elements = [];
        foreach ($items as $index => $item) {
            if ($index === $position) {
                $this->add($entity);
            }

            $this->add($item);
        }
    }

    /**
     * @api
     *
     * Merges the given collection into this collection.
     * If an entity with the same id already exists in this collection, it will be skipped.
     *
     * @param EntityCollection<E> $collection
     */
    public function merge(self $collection): void
    {
        foreach ($collection as $entity) {
            if ($this->has($entity->id)) {
                continue;
            }

            $this->add($entity);
        }
    }
}
