<?php declare(strict_types=1);

namespace Pricing\Infrastructure\Framework\Extension;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\AbstractExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PricingModuleExtension extends AbstractExtension
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->booleanNode('enabled')
            ->info('Enable or disable the module.')
            ->defaultValue(true)->end()

            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->parameters()
            ->set('module_pricing.enabled', $config['enabled'])
        ;
        $loader = new YamlFileLoader(
            $builder,
            new FileLocator(__DIR__ . '/../')
        );
        $loader->load('services.yaml');
    }

    public function getAlias(): string
    {
        return 'module_pricing';
    }
}
