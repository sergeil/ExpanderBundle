<?php

namespace Sli\ExpanderBundle\Tests\Unit;

use Sli\ExpanderBundle\Contributing\ExtensionPointsAwareBundleInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FooDummyBundle extends Bundle implements ExtensionPointsAwareBundleInterface
{
    public $map = array();

    /**
     * {@inheritdoc}
     */
    public function getExtensionPointContributions()
    {
        return $this->map;
    }
}
