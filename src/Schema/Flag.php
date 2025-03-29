<?php

declare(strict_types=1);

namespace ITB\ShopwareSdkDataCore\Schema;

use ITB\ShopwareSdkDataCore\Struct;

class Flag extends Struct
{
    public function __construct(
        public string $flag,
        public mixed $value
    ) {
    }
}
