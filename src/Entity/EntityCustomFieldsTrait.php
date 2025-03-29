<?php

declare(strict_types=1);

namespace ITB\ShopwareSdkDataCore\Entity;

trait EntityCustomFieldsTrait
{
    /**
     * @var array<string, mixed>|null
     */
    protected ?array $customFields;

    /**
     * @api
     *
     * Returns the custom fields of the entity.
     *
     * @return array<string, mixed>|null
     */
    public function getCustomFields(): ?array
    {
        return $this->customFields;
    }

    /**
     * @api
     *
     * Sets the custom fields of the entity.
     *
     * @param array<string, mixed>|null $customFields
     */
    public function setCustomFields(?array $customFields): void
    {
        $this->customFields = $customFields;
    }
}
