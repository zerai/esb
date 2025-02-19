<?php declare(strict_types=1);

namespace Cart\Core\Model;

use Ecotone\EventSourcing\Attribute\AggregateType;
use Ecotone\EventSourcing\Attribute\Stream;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\Attribute\EventSourcingAggregate;
use Ecotone\Modelling\Attribute\EventSourcingHandler;
use Ecotone\Modelling\Attribute\Identifier;
use Ecotone\Modelling\WithAggregateVersioning;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

#[EventSourcingAggregate()]
#[Stream(self::STREAM_NAME)]
#[AggregateType("cart.cart")]
class Cart
{
    public const STREAM_NAME = 'cart_cart_stream';

    use WithAggregateVersioning;

    #[Identifier]
    private UuidInterface $cartId;

    private array $cartItems = [];

    #[CommandHandler]
    public static function createCartAndAddItem(AddItem $command): array
    {
        return [
            new CartCreated($command->cartId),
            new ItemAdded(
                $command->cartId,
                $command->itemId,
                $command->productId,
                $command->description,
                $command->image,
                $command->price,
            ),
        ];
    }

    #[EventSourcingHandler]
    public function applyCartCreated(CartCreated $event): void
    {
        $this->cartId = Uuid::fromString($event->cartId);
    }

    #[CommandHandler]
    public function addItem(AddItem $command): array
    {
        if (\count($this->cartItems) >= 3) {
            throw new RuntimeException('can only add 3 items');
        }

        return [
            new ItemAdded(
                $command->cartId,
                $command->itemId,
                $command->productId,
                $command->description,
                $command->image,
                $command->price,
            ),
        ];
    }

    #[EventSourcingHandler]
    public function applyItemAdded(ItemAdded $event): void
    {
        //add item to item list
        $this->cartItems[$event->itemId] = $event->itemId;
    }

    public function id(): UuidInterface
    {
        return $this->cartId;
    }
}
