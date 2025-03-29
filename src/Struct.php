<?php

declare(strict_types=1);

namespace ITB\ShopwareSdkDataCore;

abstract class Struct
{
    /**
     * @api
     */
    public string $id;

    /**
     * @var Struct[]
     */
    protected array $extensions = [];

    /**
     * @api
     *
     * Adds a single extension (struct) to the struct.
     */
    public function addExtension(string $name, self $extension): void
    {
        $this->extensions[$name] = $extension;
    }

    /**
     * @api
     *
     * Adds multiple extensions (struct) to the struct.
     *
     * @param Struct[] $extensions
     */
    public function addExtensions(array $extensions): void
    {
        foreach ($extensions as $key => $extension) {
            if (! $extension instanceof self) {
                continue;
            }

            $this->addExtension($key, $extension);
        }
    }

    /**
     * @api
     *
     * Creates a new instance of the class and copies the properties from the given struct.
     */
    public static function createFrom(self $struct): self
    {
        try {
            $self = (new \ReflectionClass(static::class))
                ->newInstanceWithoutConstructor();
        } catch (\ReflectionException $reflectionException) {
            throw new \InvalidArgumentException($reflectionException->getMessage(), $reflectionException->getCode(), $reflectionException);
        }

        foreach (get_object_vars($struct) as $property => $value) {
            $self->{$property} = $value;
        }

        return $self;
    }

    /**
     * @api
     *
     * Returns the extension struct with the given name.
     * If the extension does not exist, null is returned.
     */
    public function getExtension(string $name): ?self
    {
        return $this->extensions[$name] ?? null;
    }

    /**
     * @api
     *
     * Returns the extension struct with the given name and type.
     * If the extension does not exist or is not of the given type, null is returned.
     */
    public function getExtensionOfType(string $name, string $type): ?self
    {
        if ($this->hasExtensionOfType($name, $type)) {
            return $this->getExtension($name);
        }

        return null;
    }

    /**
     * @api
     *
     * Returns all extensions of this struct.
     *
     * @return Struct[]
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * @api
     *
     * Checks if the struct has an extension with the given name.
     */
    public function hasExtension(string $name): bool
    {
        return isset($this->extensions[$name]);
    }

    /**
     * @api
     *
     * Checks if the struct has an extension with the given name and type.
     */
    public function hasExtensionOfType(string $name, string $type): bool
    {
        $extension = $this->getExtension($name);

        return $extension instanceof self && $extension::class === $type;
    }

    /**
     * @api
     *
     * Converts the properties of the struct into an array suitable for JSON serialization.
     * DateTimeInterface objects are formatted as RFC3339 extended strings and any nested
     * Struct objects are recursively serialized.
     *
     * @return array<array-key, mixed>
     */
    public function jsonSerialize(): array
    {
        $vars = get_object_vars($this);
        $this->convertPropertiesToJsonStringRepresentation($vars);

        return $vars;
    }

    /**
     * @api
     *
     * Removes the extension with the given name from the struct.
     */
    public function removeExtension(string $name): void
    {
        if (isset($this->extensions[$name])) {
            unset($this->extensions[$name]);
        }
    }

    /**
     * @api
     *
     * Sets the extensions of this struct.
     *
     * @param Struct[] $extensions
     */
    public function setExtensions(array $extensions): void
    {
        $this->extensions = $extensions;
    }

    /**
     * @param array<string, mixed> $array
     */
    protected function convertPropertiesToJsonStringRepresentation(array &$array): void
    {
        foreach ($array as &$value) {
            if ($value instanceof \DateTimeInterface) {
                $value = $value->format(\DateTime::RFC3339_EXTENDED);
            }

            if ($value instanceof self) {
                $value = $value->jsonSerialize();
            }
        }
    }
}
