<?php declare(strict_types=1);

namespace CartTests\Unit\Core\Model;

use Cart\Core\Model\AddItem;
use Cart\Core\Model\Cart;
use Ecotone\Lite\EcotoneLite;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Nonstandard\Uuid;
use RuntimeException;

class Max3itemsPerCartTest extends TestCase
{
    private const CART_UUID = '048a23d9-db59-4d49-87e0-36a05ee08593';

    public function test_Max3itemspercart(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('can only add 3 items');

        $CartId = Uuid::fromString(self::CART_UUID)->toString();

        // GIVEN
        $sut = EcotoneLite::bootstrapFlowTesting([Cart::class])
            ->sendCommand(new AddItem(
                $CartId,
                Uuid::uuid4()->__toString(),
                Uuid::uuid4()->__toString(),
                'irrelevant',
                'irrelevant',
                '9.99'
            ))
            ->sendCommand(new AddItem(
                $CartId,
                Uuid::uuid4()->__toString(),
                Uuid::uuid4()->__toString(),
                'irrelevant',
                'irrelevant',
                '9.99'
            ))
            ->sendCommand(new AddItem(
                $CartId,
                Uuid::uuid4()->__toString(),
                Uuid::uuid4()->__toString(),
                'irrelevant',
                'irrelevant',
                '9.99'
            ));

        // WHEN
        $sut->sendCommand(new AddItem(
            $CartId,
            Uuid::uuid4()->__toString(),
            Uuid::uuid4()->__toString(),
            'irrelevant',
            'irrelevant',
            '9.99'
        ));

        // THEN
        // throw exception
    }
}
