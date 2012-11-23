<?php

namespace Sli\ExpanderBundle\Tests\Ext;

use Sli\ExpanderBundle\Ext\CompilerPass;
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

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class CompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function test__Construct()
    {
        $cp = new CompilerPass('foo');
        $this->assertSame('foo', $cp->getProviderServiceId());
        $this->assertSame('foo', $cp->getContributorServiceTagName());
    }

    public function testProcess()
    {
        $cb = new MockContainerBuilder();
        $cb->services = array(
            'service1foo' => 'def',
            'service2bar' => 'def'
        );

        $cp = new CompilerPass('fooServiceId', 'barServiceId');
        $cp->process($cb);

        $this->assertEquals(1, count($cb->definitions));
        $this->assertArrayHasKey('fooServiceId', $cb->definitions);

        /* @var Symfony\Component\DependencyInjection\Definition $provider */
        $provider = $cb->definitions['fooServiceId'];
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Definition', $provider);
        $calls = $provider->getMethodCalls();
        $this->assertEquals(2, count($calls));

        $this->assertContributor($calls[0], 'service1foo');
        $this->assertContributor($calls[1], 'service2bar');
    }

    private function assertContributor(array $methodCall, $refServiceId)
    {
        $this->assertEquals(2, count($methodCall));
        $this->assertEquals('addContributor', $methodCall[0]);
        /* @var \Symfony\Component\DependencyInjection\Reference $ref */
        $ref = $methodCall[1][0];
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $ref);
        $this->assertEquals($refServiceId, $ref->__toString());
    }
}
