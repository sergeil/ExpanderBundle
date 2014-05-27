<?php

namespace Sli\ExpanderBundle\DependencyInjection;

use Sli\ExpanderBundle\Ext\ExtensionPoint;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
interface ExtensionPointAwareCompilerPassInterface
{
    /**
     * Must return an instance of extension point that this compiler pass is attached to.
     *
     * @return ExtensionPoint
     */
    public function getExtensionPoint();
}