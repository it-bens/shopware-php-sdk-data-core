<?php

declare(strict_types=1);

namespace ITB\ShopwareSdkDataCore\Schema;

use ITB\ShopwareSdkDataCore\Collection;

/**
 * @extends Collection<Property>
 */
class PropertyCollection extends Collection
{
    /**
     * @param iterable<Property> $properties
     */
    public function __construct(iterable $properties = [])
    {
        $properties = $properties instanceof \Traversable ? iterator_to_array($properties) : $properties;
        parent::__construct(array_combine(array_column($properties, 'name'), $properties));
    }

    #[\Override]
    public function getExpectedClass(): string
    {
        return Property::class;
    }
}
