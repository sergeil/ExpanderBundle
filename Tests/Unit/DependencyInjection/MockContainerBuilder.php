<?php

namespace Sli\ExpanderBundle\Tests\Unit\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class MockContainerBuilder extends ContainerBuilder
{
    public $definitions = array();

    public $services = array();

    public function findTaggedServiceIds($name)
    {
        return $this->services;
    }

    public function addDefinitions(array $definitions)
    {
        $this->definitions = array_merge($this->definitions, $definitions);
    }
}
