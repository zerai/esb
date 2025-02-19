<?php declare(strict_types=1);

namespace CartTests\Unit\Core\Model;

use Cart\Core\Model\AddItem;
use Cart\Core\Model\Cart;
use Cart\Core\Model\CartCreated;
use Cart\Core\Model\ItemAdded;
use Ecotone\Lite\EcotoneLite;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CartTest extends TestCase
{
    private const CART_UUID = '048a23d9-db59-4d49-87e0-36a05ee08593';

    private const ITEM_UUID = '048a23d9-db59-4d49-87e0-36a05ee08593';

    private const PRODUCT_UUID = '048a23d9-db59-4d49-87e0-36a05ee08593';

    private const DESCRIPTION = 'A produt item description';

    private const IMAGE = 'product-image.jpeg';

    private const PRICE = '9.99';

    public function test_message_flow_when_add_an_item_to_new_cart(): void
    {
        $aCartId = Uuid::fromString(self::CART_UUID)->toString();

        $expectedEvent = [
            new CartCreated($aCartId),
            new ItemAdded($aCartId, self::ITEM_UUID, self::PRODUCT_UUID, self::DESCRIPTION, self::IMAGE, self::PRICE),
        ];

        $emittedEvents = EcotoneLite::bootstrapFlowTesting([Cart::class])
            ->sendCommand(new AddItem(
                $aCartId,
                self::ITEM_UUID,
                self::PRODUCT_UUID,
                self::DESCRIPTION,
                self::IMAGE,
                self::PRICE
            ))
            ->getRecordedEvents();

        self::assertEquals($expectedEvent, $emittedEvents);
    }

    public function test_message_flow_when_add_an_item_to_already_existing_cart(): void
    {
        $aCartId = Uuid::fromString(self::CART_UUID)->toString();

        $expectedEvent = [
            new ItemAdded($aCartId, self::ITEM_UUID, self::PRODUCT_UUID, self::DESCRIPTION, self::IMAGE, self::PRICE),
        ];

        $emittedEvents = EcotoneLite::bootstrapFlowTesting([Cart::class])
            ->withEventsFor($aCartId, Cart::class, [
                new CartCreated($aCartId),
                new ItemAdded($aCartId, Uuid::uuid4()->__toString(), Uuid::uuid4()->__toString(), 'irrelevant', 'irrelevant.jpeg', '1,00'),
            ])
            ->sendCommand(new AddItem(
                $aCartId,
                self::ITEM_UUID,
                self::PRODUCT_UUID,
                self::DESCRIPTION,
                self::IMAGE,
                self::PRICE
            ))
            ->getRecordedEvents();

        self::assertEquals($expectedEvent, $emittedEvents);
    }

    public function test_should_add_an_item(): void
    {
        $expectedCartId = Uuid::fromString(self::CART_UUID)->toString();

        $sut = EcotoneLite::bootstrapFlowTesting([Cart::class])
            ->sendCommand(new AddItem(
                $expectedCartId,
                self::ITEM_UUID,
                self::PRODUCT_UUID,
                self::DESCRIPTION,
                self::IMAGE,
                self::PRICE
            ))
            ->getAggregate(Cart::class, $expectedCartId);

        self::assertEquals($expectedCartId, $sut->id());
    }
}
