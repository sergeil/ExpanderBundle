<?php

namespace Sli\ExpanderBundle\Tests\Unit\Contributing;

use Sli\ExpanderBundle\Contributing\ExtensionPointsAwareBundlesCollectorCompilerPass;
use Sli\ExpanderBundle\Tests\Unit\FooDummyBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DummyContainerBuilder extends ContainerBuilder
{
    /* @var Definition[] */
    public $definitions;

    // override
    public function setDefinition($id, Definition $definition)
    {
        $this->definitions[$id] = $definition;
    }
}

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ExtensionPointsAwareBundlesCollectorCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $bundle1 = new FooDummyBundle();
        $bundle1->map = array(
            'foo_ep' => array(
            ),
            'bar_ep' => array(
            ),
        );

        $bundle2 = \Phake::mock('Symfony\Component\HttpKernel\Bundle\BundleInterface');
        $bundle3 = new FooDummyBundle();
        $bundle3->map = array(
            'baz_ep' => array(),
        );

        $kernel = \Phake::mock('Symfony\Component\HttpKernel\KernelInterface');
        \Phake::when($kernel)->getBundles()->thenReturn(array($bundle1, $bundle2,  $bundle3));

        $containerBuilder = new DummyContainerBuilder();

        $cp = new ExtensionPointsAwareBundlesCollectorCompilerPass($kernel);
        $cp->process($containerBuilder);

        $this->assertEquals(3, count($containerBuilder->definitions));
        foreach ($containerBuilder->definitions as $definition) {
            $this->assertInstanceOf('Symfony\Component\DependencyInjection\Definition', $definition);

            $args = $definition->getArguments();
            $this->assertEquals(3, count($args));

            $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $args[0]);
            /* @var Reference $kernelArg*/
            $kernelArg = $args[0];
            $this->assertEquals('kernel', (string) $kernelArg);

            $this->assertNotNull($args[1]);
        }

        /* @var Definition[] $definitions */
        $definitions = array_values($containerBuilder->definitions);

        $definition1 = $definitions[0];
        $this->assertEquals($bundle1->getName(), $definition1->getArgument(1));
        $this->assertEquals('foo_ep', $definition1->getArgument(2));

        $definition2 = $definitions[1];
        $this->assertEquals($bundle1->getName(), $definition2->getArgument(1));
        $this->assertEquals('bar_ep', $definition2->getArgument(2));

        $definition3 = $definitions[2];
        $this->assertEquals($bundle1->getName(), $definition3->getArgument(1));
        $this->assertEquals('baz_ep', $definition3->getArgument(2));
    }
}
