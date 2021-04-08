<?php

namespace ICS\SsiBundle\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SsiExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration(false);
        $configs = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config/'));
        $loader->load('services.yaml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config/'));
        $bundles = $container->getParameter('kernel.bundles');

        $loader->load('security.yaml');

        if (isset($bundles['MonologBundle'])) {
            $loader->load('monolog.yaml');
        }

        if (isset($bundles['FrameworkExtraBundle'])) {
            $loader->load('framework_extra.yaml');
        }

        if (isset($bundles['DashboardBundle'])) {
            $loader->load('dashboard.yaml');
        }
    }
}
