<?php declare(strict_types=1);

namespace Cart\AdapterForHttp;

use Cart\Core\Model\AddItem;
use Ecotone\Modelling\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AddItemController extends AbstractController
{
    private const CART_UUID = '048a23d9-db59-4d49-87e0-36a05ee08594';

    private const ITEM_UUID = '048a23d9-db59-4d49-87e0-36a05ee08593';

    private const PRODUCT_UUID = '048a23d9-db59-4d49-87e0-36a05ee08593';

    private const DESCRIPTION = 'A produt item description';

    private const IMAGE = 'product-image.jpeg';

    private const PRICE = '9.99';

    #[Route('/additem.html', name: 'cart_add_item')]
    public function index(CommandBus $commandBus): Response
    {
        $command = new AddItem(
            self::CART_UUID,
            self::ITEM_UUID,
            self::PRODUCT_UUID,
            self::DESCRIPTION,
            self::IMAGE,
            self::PRICE
        );

        $commandBus->send($command);
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }
}
