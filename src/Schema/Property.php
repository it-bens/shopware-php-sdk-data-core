<?php

declare(strict_types=1);

namespace ITB\ShopwareSdkDataCore\Schema;

use ITB\ShopwareSdkDataCore\Struct;

class Property extends Struct
{
    private const array JSON_TYPES = ['json_list', 'json_object'];

    private const array SCALAR_TYPES = ['uuid', 'int', 'text', 'password', 'float', 'string', 'blob', 'boolean', 'date'];

    /**
     * @api
     */
    public ?string $entity;

    /**
     * @api
     */
    public ?string $local;

    /**
     * @api
     */
    public ?string $localField;

    /**
     * @api
     */
    public ?string $mapping;

    /**
     * @api
     *
     * @var array<array-key, mixed>|null
     */
    public ?array $properties;

    /**
     * @api
     */
    public ?string $reference;

    /**
     * @api
     */
    public ?string $referenceField;

    /**
     * @api
     */
    public ?string $relation;

    /**
     * @param array{
     *     relation?: string,
     *     local?: string,
     *     localField?: string,
     *     reference?: string,
     *     referenceField?: string,
     *     entity?: string,
     *     mapping?: string,
     *     properties?: array<array-key, mixed>|null
     * } $properties
     */
    public function __construct(
        public string $name,
        public string $type,
        public FlagCollection $flags,
        array $properties = []
    ) {
        $this->relation = $properties['relation'] ?? null;
        $this->local = $properties['local'] ?? null;
        $this->localField = $properties['localField'] ?? null;
        $this->reference = $properties['reference'] ?? null;
        $this->referenceField = $properties['referenceField'] ?? null;
        $this->entity = $properties['entity'] ?? null;
        $this->mapping = $properties['mapping'] ?? null;
        $this->properties = $properties['properties'] ?? null;
    }

    /**
     * @api
     *
     * Checks if the property is a relation to another entity.
     */
    public function isAssociation(): bool
    {
        return $this->relation !== null && $this->type === 'association';
    }

    /**
     * @api
     *
     * Checks if the property is either a JSON list or a JSON object.
     */
    public function isJsonField(): bool
    {
        return in_array($this->type, self::JSON_TYPES);
    }

    /**
     * @api
     *
     * Checks if the property is a JSON list.
     */
    public function isJsonListField(): bool
    {
        return $this->type === 'json_list';
    }

    /**
     * @api
     *
     * Checks if the property is a JSON object.
     */
    public function isJsonObjectField(): bool
    {
        return $this->type === 'json_object';
    }

    /**
     * @api
     *
     * Checks if the property is a scalar field.
     * Scalar fields are: uuid, int, text, password, float, string, blob, boolean and date.
     */
    public function isScalarField(): bool
    {
        return in_array($this->type, self::SCALAR_TYPES);
    }

    /**
     * @api
     *
     * Checks if the property is a string field.
     */
    public function isStringField(): bool
    {
        return in_array($this->type, ['uuid', 'string', 'password', 'text', 'blob']);
    }

    /**
     * @api
     *
     * Checks if the property is a either one-to-many or many-to-many association.
     */
    public function isToManyAssociation(): bool
    {
        return $this->isAssociation() && $this->entity !== null && in_array($this->relation, ['one_to_many', 'many_to_many']);
    }

    /**
     * @api
     *
     * Checks if the property is either a one-to-one or many-to-one association.
     */
    public function isToOneAssociation(): bool
    {
        return $this->isAssociation() && $this->entity !== null && in_array($this->relation, ['many_to_one', 'one_to_one']);
    }

    /**
     * @api
     *
     * Checks if the property is a translatable field.
     * Translatable fields are of type string or text and are marked as translatable.
     */
    public function isTranslatableField(): bool
    {
        return ($this->type === 'string' || $this->type === 'text') && $this->flags->has('translatable');
    }
}
