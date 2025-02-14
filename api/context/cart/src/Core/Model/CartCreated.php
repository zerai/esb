<?php declare(strict_types=1);

namespace Cart\Core\Model;

class CartCreated
{
    public function __construct(
        public readonly string $cartId
    ) {
    }
}
