<?php declare(strict_types=1);

namespace Cart\Core\Model;

class ItemAdded
{
    public function __construct(
        public readonly string $cartId,
        public readonly string $itemId,
        public readonly string $productId,
        public readonly string $description,
        public readonly string $image,
        public readonly string $price
    ) {
    }
}
