<?php

declare(strict_types=1);

namespace ITB\ShopwareSdkDataCore\Entity;

use ITB\ShopwareSdkDataCore\Struct;

abstract class Entity extends Struct
{
    use EntityCustomFieldsTrait;

    private const array NON_STRUCT_PROPERTY_TYPES = ['string', 'array', 'object', 'resource', 'bool', 'int', 'float', 'double'];

    /**
     * @api
     */
    public string $_uniqueIdentifier;

    /**
     * @api
     */
    public ?string $apiAlias = null;

    /**
     * @api
     */
    public ?\DateTimeInterface $createdAt = null;

    /**
     * @api
     */
    public string $id;

    /**
     * @api
     */
    public ?\DateTimeInterface $updatedAt = null;

    /**
     * @api
     */
    public ?string $versionId = null;

    /**
     * @var array<string, mixed>
     */
    protected array $translated = [];

    private ?string $_entityName = null;

    /**
     * @api
     *
     * Adds a translated property to the entity.
     */
    public function addTranslated(string $key, mixed $value): void
    {
        $this->translated[$key] = $value;
    }

    /**
     * @api
     *
     * Assigns properties to the entity based on the given attribute array.
     *
     * @param array<string, mixed> $attributes
     */
    public function assignProperties(array $attributes): self
    {
        foreach ($attributes as $attributeKey => $attributeValue) {
            if ($attributeKey === 'id') {
                if (! is_string($attributeValue)) {
                    throw new \InvalidArgumentException('Expected the id to be a string, got ' . gettype($attributeValue));
                }

                $this->id = $attributeValue;

                continue;
            }

            try {
                $this->setProperty($attributeKey, $attributeValue);
            } catch (\Error | \Exception) {
                // nth
            }
        }

        return $this;
    }

    /**
     * @api
     *
     * Creates a new instance of the given entity class and sets the properties from the given attribute array.
     *
     * @param array<array-key, mixed> $attributes
     */
    public static function createFromArray(string $expectedEntityClass, array $attributes = []): self
    {
        /** @var Entity $expectedEntity */
        $expectedEntity = new $expectedEntityClass();

        foreach ($attributes as $attributeKey => $attributeValue) {
            if (! is_string($attributeKey)) {
                continue;
            }

            $expectedEntity->setProperty($attributeKey, $attributeValue);
        }

        return $expectedEntity;
    }

    /**
     * @api
     *
     * Returns the entity name.
     */
    public function getEntityName(): ?string
    {
        return $this->_entityName;
    }

    /**
     * @api
     *
     * Returns the value of the given property or null if it does not exist.
     */
    public function getProperty(string $property): mixed
    {
        if ($this->has($property)) {
            return $this->{$property};
        }

        return null;
    }

    /**
     * @api
     *
     * Returns the translated properties of the entity.
     *
     * @return array<string, mixed>
     */
    public function getTranslated(): array
    {
        return $this->translated;
    }

    /**
     * @api
     *
     * Returns the translation for the given field or null if it does not exist.
     */
    public function getTranslation(string $field): mixed
    {
        return $this->translated[$field] ?? null;
    }

    /**
     * @api
     *
     * Checks if the entity has a property.
     */
    public function has(string $property): bool
    {
        return property_exists($this, $property);
    }

    /**
     * @api
     *
     * Sets the entity name.
     */
    public function internalSetEntityName(string $entityName): self
    {
        $this->_entityName = $entityName;

        return $this;
    }

    /**
     * @api
     *
     * Sets the value of the given property. If the property does not exist, it is dynamically added to the entity.
     * Properties with union or intersection types are not supported.
     * If the property type allows null and the value is null, the property is set directly.
     * If the property type is a non-struct type (e.g., string, array, object, etc.), the property is set directly.
     * If the property type is a subclass of Struct and the value is an EntityCollection or another Struct, it is converted accordingly.
     * If the property type is a subclass of EntityCollection, the value is converted to an array of entities.
     * If the property type implements DateTimeInterface, the value is converted to a DateTimeImmutable object.
     */
    public function setProperty(string $property, mixed $value): void
    {
        /**
         * The block handles properties that are not defined in the class.
         * It's a fallback to circumvent silent skipping of properties.
         * From PHP 8.2 on this will throw a deprecation warning.
         * PHP 9 will most likely throw an error.
         */
        if (! $this->has($property)) {
            $this->{$property} = $value;
            return;
        }

        $propertyReflection = new \ReflectionProperty($this, $property);
        $type = $propertyReflection->getType();
        if ($type !== null && ! $type instanceof \ReflectionNamedType) {
            throw new \InvalidArgumentException('Properties with union types and intersection types are not supported yet.');
        }

        if (! $type instanceof \ReflectionNamedType || ($type->allowsNull() && $value === null)) {
            $this->{$property} = $value;
            return;
        }

        /** @var class-string $typeName */
        $typeName = $type->getName();

        if (in_array($typeName, self::NON_STRUCT_PROPERTY_TYPES)) {
            $this->{$property} = $value;
            return;
        }

        if (is_subclass_of($typeName, Struct::class) && ($value instanceof EntityCollection || $value instanceof self)) {
            $original = $value;
            $value = $typeName::createFrom($value);

            if ($original instanceof self && $value instanceof self && $entity = $original->getEntityName()) {
                $value->internalSetEntityName($entity);
            }

            $this->{$property} = $value;
            return;
        }

        $reflectionClass = new \ReflectionClass($typeName);
        $dummyType = $reflectionClass->isInstantiable() ? new $typeName() : null;

        switch (true) {
            case $dummyType instanceof self:
                if (! is_array($value)) {
                    throw new \InvalidArgumentException('Expected the value to be an array, got ' . gettype($value));
                }

                $this->{$property} = self::createFromArray($typeName, $value);
                break;
            case $dummyType instanceof EntityCollection:
                if (! is_array($value)) {
                    throw new \InvalidArgumentException('Expected the value to be an array, got ' . gettype($value));
                }

                $value = array_map(static function (mixed $item) use ($dummyType): Entity {
                    if (! is_array($item)) {
                        throw new \InvalidArgumentException('Expected the item to be an array, got ' . gettype($item));
                    }

                    return self::createFromArray($dummyType->getExpectedClass(), $item);
                }, $value);
                $this->{$property} = new $dummyType($value);
                break;
            case $reflectionClass->implementsInterface(\DateTimeInterface::class):
                if (! is_string($value)) {
                    throw new \InvalidArgumentException('Expected the value to be a string, got ' . gettype($value));
                }

                $this->{$property} = new \DateTimeImmutable($value);

                break;
            default:
                $this->{$property} = $value;
        }
    }

    /**
     * @api
     *
     * Sets the translated properties of the entity.
     *
     * @param array<string, mixed> $translated
     */
    public function setTranslated(array $translated): void
    {
        $this->translated = $translated;
    }
}
