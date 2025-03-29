<?php

declare(strict_types=1);

namespace ITB\ShopwareSdkDataCore\Schema;

use ITB\ShopwareSdkDataCore\Struct;

class Schema extends Struct
{
    public function __construct(
        public string $entity,
        public PropertyCollection $properties
    ) {
    }

    /**
     * @api
     *
     * Creates a schema object from raw data.
     * The properties array should contain the property name as key and an array with the type and flags as value.
     *
     * @param array{flags?: array<string, mixed>, type: string}[] $properties
     */
    public static function createFromRaw(string $entity, array $properties): self
    {
        $propertiesCollection = [];

        foreach ($properties as $propertyName => $property) {
            $flags = $property['flags'] ?? [];
            $flagCollection = [];

            foreach ($flags as $key => $flag) {
                $flagCollection[$key] = new Flag($key, $flag);
            }

            $propertiesCollection[$propertyName] = new Property(
                $propertyName,
                $property['type'],
                new FlagCollection($flagCollection),
                $property
            );
        }

        return new self($entity, new PropertyCollection($propertiesCollection));
    }
}
