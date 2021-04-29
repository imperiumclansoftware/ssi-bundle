<?php

namespace ICS\SsiBundle;

/**
 * File for bundle configuration
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle class
 *
 * @package SsiBundle
 */
class SsiBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}