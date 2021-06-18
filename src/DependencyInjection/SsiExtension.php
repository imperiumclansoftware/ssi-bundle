<?php

namespace ICS\SsiBundle\DependencyInjection;

/**
 * File for SsiBundle extension configuration
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

/**
 * SsiBundle extension
 *
 * @package SsiBundle\DependencyInjection
 */
class SsiExtension extends Extension implements PrependExtensionInterface
{

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config/'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $configs = $this->processConfiguration($configuration, $configs);
        $container->setParameter('ssi', $configs);
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

        if(isset($_ENV['KEYCLOAK_URL']) && $_ENV['KEYCLOAK_URL']!=null)
        {
            $loader->load('security_keyloak.yaml');
            $loader->load('knpu_oauth2_client.yaml');
        }

        if(isset($_ENV['ACTIVE_DIRECTORY_HOST']) && $_ENV['ACTIVE_DIRECTORY_HOST']!=null)
        {
            $loader->load('security_ad.yaml');
        }
    }
}
