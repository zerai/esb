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

};
