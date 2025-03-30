<?php

declare(strict_types=1);

namespace ITB\ShopwareSdkDataCore;

interface Parsable
{
    /**
     * @api
     *
     * @return array<array-key, mixed>
     */
    public function parse(): array;
}
