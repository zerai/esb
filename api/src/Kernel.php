<?php

namespace App;

use Cart\Infrastructure\Framework\Extension\CartModuleExtension;
use Inventory\Infrastructure\Framework\Extension\InventoryModuleExtension;
use Pricing\Infrastructure\Framework\Extension\PricingModuleExtension;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container)
    {
        $container->registerExtension(new CartModuleExtension());
        $container->registerExtension(new InventoryModuleExtension());
        $container->registerExtension(new PricingModuleExtension());
    }
}
