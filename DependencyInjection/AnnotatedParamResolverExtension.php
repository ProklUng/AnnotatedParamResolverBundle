<?php

namespace Prokl\AnnotatedParamResolverBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class AnnotatedParamResolverExtension
 * @package Prokl\AnnotatedParamResolverBundle\DependencyInjection
 *
 * @since 20.04.2021
 */
class AnnotatedParamResolverExtension extends Extension
{
    private const DIR_CONFIG = '/../Resources/config';

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container) : void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . self::DIR_CONFIG)
        );

        if ($container->getParameter('kernel.environment') === 'dev') {
            $loader->load('dev/services.yaml');
        }

        $loader->load('annotations.yaml');
        $loader->load('resolvers.yaml');
    }

    /**
     * @inheritDoc
     */
    public function getAlias() : string
    {
        return 'annotated_param_resolver';
    }
}
