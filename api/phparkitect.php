<?php

declare(strict_types=1);

use Arkitect\ClassSet;
use Arkitect\CLI\Config;
use Arkitect\Expression\ForClasses\HaveNameMatching;
use Arkitect\Expression\ForClasses\NotHaveDependencyOutsideNamespace;
use Arkitect\Expression\ForClasses\ResideInOneOfTheseNamespaces;
use Arkitect\RuleBuilders\Architecture\Architecture;
use Arkitect\Rules\Rule;

return static function (Config $config): void {
    $classSet = ClassSet::fromDir(__DIR__.'/src');

    $layeredArchitectureRules = Architecture::withComponents()
        ->component('Controller')->definedBy('App\Controller\*')
        ->component('Service')->definedBy('App\Service\*')
        ->component('Repository')->definedBy('App\Repository\*')
        ->component('Entity')->definedBy('App\Entity\*')

        ->where('Controller')->mayDependOnComponents('Service', 'Entity')
        ->where('Service')->mayDependOnComponents('Repository', 'Entity')
        ->where('Repository')->mayDependOnComponents('Entity')
        ->where('Entity')->shouldNotDependOnAnyComponent()

        ->rules();

    $serviceNamingRule = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\Service'))
        ->should(new HaveNameMatching('*Service'))
        ->because('we want uniform naming for services');

    $repositoryNamingRule = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('App\Repository'))
        ->should(new HaveNameMatching('*Repository'))
        ->because('we want uniform naming for repositories');

    $config->add($classSet, $serviceNamingRule, $repositoryNamingRule, ...$layeredArchitectureRules);



    /**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *
     *      CART CONTEXT
     *
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     */
    $cartContextClassSet = ClassSet::fromDir(__DIR__ . '/context/cart/src');

    $allowedPhpDependencies = require_once __DIR__ . '/tools/ark/config/cart/allowed_php_deps_in_core.php';
    $allowedVendorDependenciesInCartContextCore = require_once __DIR__ . '/tools/ark/config/cart/allowed_vendor_deps_in_core.php';
    $allowedVendorDependenciesInCartContextAdapters = require_once __DIR__ . '/tools/ark/config/cart/allowed_vendor_deps_in_adapters.php';

    $cartContextPortAndAdapterArchitectureRules = Architecture::withComponents()
        ->component('Core')->definedBy('Cart\Core\*')
        ->component('Adapters')->definedBy('Cart\AdapterFor*')
        ->component('Infrastructure')->definedBy('Cart\Infrastructure\*')

        ->where('Infrastructure')->mayDependOnComponents('Core')
        ->where('Adapters')->mayDependOnComponents('Core', 'Infrastructure')
        ->where('Core')->shouldNotDependOnAnyComponent()
        ->rules();


    $allowedDependenciesInCartContextCoreCode = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInCartContextCore);
    $cartCoreIsolationRule = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('Cart\Core'))
        ->should(new NotHaveDependencyOutsideNamespace('Cart\Core', $allowedDependenciesInCartContextCoreCode))
        ->because('we want isolate our cart core domain from external world.');


    $allowedDependenciesInCartContextAdapters = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInCartContextAdapters);
    $cartAdaptersIsolationRule = Rule::allClasses()
        //->except('Cart\Adapter\Http\Web\FooController', 'Cart\Adapter\Packagist\FooApiAdapter')
        ->that(new ResideInOneOfTheseNamespaces('Cart\Adapter*'))
        ->should(new NotHaveDependencyOutsideNamespace('Cart\Core', $allowedDependenciesInCartContextAdapters))
        ->because('we want isolate our cart Adapters from ever growing dependencies.');

    $config->add($cartContextClassSet, $cartCoreIsolationRule, $cartAdaptersIsolationRule, ...$cartContextPortAndAdapterArchitectureRules);



    /**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *
     *      INVENTORY CONTEXT
     *
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     */
    $inventoryContextClassSet = ClassSet::fromDir(__DIR__ . '/context/inventory/src');

    $allowedPhpDependencies = require_once __DIR__ . '/tools/ark/config/inventory/allowed_php_deps_in_core.php';
    $allowedVendorDependenciesInInventoryContextCore = require_once __DIR__ . '/tools/ark/config/inventory/allowed_vendor_deps_in_core.php';
    $allowedVendorDependenciesInInventoryContextAdapters = require_once __DIR__ . '/tools/ark/config/inventory/allowed_vendor_deps_in_adapters.php';

    $inventoryContextPortAndAdapterArchitectureRules = Architecture::withComponents()
        ->component('Core')->definedBy('Inventory\Core\*')
        ->component('Adapters')->definedBy('Inventory\AdapterFor*')
        ->component('Infrastructure')->definedBy('Inventory\Infrastructure\*')

        ->where('Infrastructure')->mayDependOnComponents('Core')
        ->where('Adapters')->mayDependOnComponents('Core', 'Infrastructure')
        ->where('Core')->shouldNotDependOnAnyComponent()
        ->rules();


    $allowedDependenciesInInventoryContextCoreCode = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInInventoryContextCore);
    $inventoryCoreIsolationRule = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('Inventort\Core'))
        ->should(new NotHaveDependencyOutsideNamespace('Inventort\Core', $allowedDependenciesInInventoryContextCoreCode))
        ->because('we want isolate our inventory core domain from external world.');


    $allowedDependenciesInInventoryContextAdapters = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInInventoryContextAdapters);
    $inventoryAdaptersIsolationRule = Rule::allClasses()
        //->except('Inventory\Adapter\Http\Web\FooController', 'Inventory\Adapter\Packagist\FooApiAdapter')
        ->that(new ResideInOneOfTheseNamespaces('Inventory\Adapter*'))
        ->should(new NotHaveDependencyOutsideNamespace('Inventory\Core', $allowedDependenciesInInventoryContextAdapters))
        ->because('we want isolate our inventory Adapters from ever growing dependencies.');

    $config->add($inventoryContextClassSet, $inventoryCoreIsolationRule, $inventoryAdaptersIsolationRule, ...$inventoryContextPortAndAdapterArchitectureRules);




    /**++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *
     *      PRICING CONTEXT
     *
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     *++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*
     */
    $pricingContextClassSet = ClassSet::fromDir(__DIR__ . '/context/pricing/src');

    $allowedPhpDependencies = require_once __DIR__ . '/tools/ark/config/pricing/allowed_php_deps_in_core.php';
    $allowedVendorDependenciesInPricingContextCore = require_once __DIR__ . '/tools/ark/config/pricing/allowed_vendor_deps_in_core.php';
    $allowedVendorDependenciesInPricingContextAdapters = require_once __DIR__ . '/tools/ark/config/pricing/allowed_vendor_deps_in_adapters.php';

    $pricingContextPortAndAdapterArchitectureRules = Architecture::withComponents()
        ->component('Core')->definedBy('Inventory\Core\*')
        ->component('Adapters')->definedBy('Inventory\AdapterFor*')
        ->component('Infrastructure')->definedBy('Inventory\Infrastructure\*')

        ->where('Infrastructure')->mayDependOnComponents('Core')
        ->where('Adapters')->mayDependOnComponents('Core', 'Infrastructure')
        ->where('Core')->shouldNotDependOnAnyComponent()
        ->rules();


    $allowedDependenciesInPricingContextCoreCode = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInPricingContextCore);
    $pricingCoreIsolationRule = Rule::allClasses()
        ->that(new ResideInOneOfTheseNamespaces('Pricing\Core'))
        ->should(new NotHaveDependencyOutsideNamespace('Pricing\Core', $allowedDependenciesInPricingContextCoreCode))
        ->because('we want isolate our pricing core domain from external world.');


    $allowedDependenciesInPricingContextAdapters = array_merge($allowedPhpDependencies, $allowedVendorDependenciesInPricingContextAdapters);
    $pricingAdaptersIsolationRule = Rule::allClasses()
        //->except('Inventory\Adapter\Http\Web\FooController', 'Inventory\Adapter\Packagist\FooApiAdapter')
        ->that(new ResideInOneOfTheseNamespaces('Pricing\Adapter*'))
        ->should(new NotHaveDependencyOutsideNamespace('Pricing\Core', $allowedDependenciesInPricingContextAdapters))
        ->because('we want isolate our pricing Adapters from ever growing dependencies.');

    $config->add($pricingContextClassSet, $pricingCoreIsolationRule, $pricingAdaptersIsolationRule, ...$pricingContextPortAndAdapterArchitectureRules);
};
