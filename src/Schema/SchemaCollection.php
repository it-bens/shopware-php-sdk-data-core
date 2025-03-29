<?php

declare(strict_types=1);

namespace ITB\ShopwareSdkDataCore\Schema;

use ITB\ShopwareSdkDataCore\Collection;

/**
 * @extends Collection<Schema>
 */
class SchemaCollection extends Collection
{
    /**
     * @param iterable<Schema> $schemas
     */
    public function __construct(iterable $schemas = [])
    {
        $schemas = $schemas instanceof \Traversable ? iterator_to_array($schemas) : $schemas;
        parent::__construct(array_combine(array_column($schemas, 'entity'), $schemas));
    }

    #[\Override]
    public function getExpectedClass(): string
    {
        return Schema::class;
    }
}
