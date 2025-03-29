<?php

declare(strict_types=1);

namespace ITB\ShopwareSdkDataCore\Schema;

use ITB\ShopwareSdkDataCore\Collection;

/**
 * @extends Collection<Flag>
 */
class FlagCollection extends Collection
{
    /**
     * @param iterable<Flag> $flags
     */
    public function __construct(iterable $flags = [])
    {
        $flags = $flags instanceof \Traversable ? iterator_to_array($flags) : $flags;
        parent::__construct(array_combine(array_column($flags, 'flag'), $flags));
    }

    #[\Override]
    public function getExpectedClass(): string
    {
        return Flag::class;
    }
}
