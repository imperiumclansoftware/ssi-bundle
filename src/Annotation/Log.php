<?php

namespace ICS\SsiBundle\Annotation;

/**
 * File for @log annotation
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Doctrine\Common\Annotations\Annotation;

/**
 * Log anootation class
 *
 * @package SsiBundle\Annotation
 * @Annotation
 * @Target("CLASS")
 */
class Log
{
    /**
     * @Required
     *
     * @var array
     */
    public $actions;

    /**
     * @Required
     *
     * @var string
     */
    public $property;

    /**
     * Get the value of actions
     *
     * @return  array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Get the value of property
     *
     * @return  string
     */
    public function getProperty()
    {
        return $this->property;
    }
}
