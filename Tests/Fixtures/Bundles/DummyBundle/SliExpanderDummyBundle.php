<?php

namespace Sli\ExpanderBundle\Tests\Fixtures\Bundles\DummyBundle;

use Sli\ExpanderBundle\Contributing\ExtensionPointsAwareBundleInterface;
use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Sli\ExpanderBundle\Tests\Fixtures\Bundles\DummyBundle\DependencyInjection\SliExpanderDummyExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class SliExpanderDummyBundle extends Bundle implements ExtensionPointsAwareBundleInterface
{
    // override
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->registerExtension(new SliExpanderDummyExtension());

        $ep = new ExtensionPoint('sli_expander.dummy_resources_provider');

        $container->addCompilerPass($ep->createCompilerPass());
    }

    /**
     * @inheritDoc
     */
    public function getExtensionPointContributions()
    {
        return array(
            'sli_expander.dummy_resources_provider' => array(
                'baz_resource'
            )
        );
    }
}
