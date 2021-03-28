<?php

namespace ICS\SsiBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle class
 */
class SsiBundle extends Bundle
{
    public function build(ContainerBuilder $builder)
    {

    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}