<?php

declare(strict_types=1);

namespace ITB\ShopwareSdkDataCore\Entity;

use ITB\ShopwareSdkDataCore\Schema\Schema;

interface EntityDefinition
{
    /**
     * @api
     */
    public function getEntityClass(): string;

    /**
     * @api
     */
    public function getEntityCollection(): string;

    /**
     * @api
     */
    public function getEntityName(): string;

    /**
     * @api
     */
    public function getSchema(): Schema;
}
