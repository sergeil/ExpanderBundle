<?php

namespace Sli\ExpanderBundle\Tests\Unit\DependencyInjection;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class CompositeContributorsProviderCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    public function test__Construct()
    {
        $cp = new CompositeContributorsProviderCompilerPass('foo');
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

        $cp = new CompositeContributorsProviderCompilerPass('fooServiceId', 'barServiceId');
        $cp->process($cb);

        $this->assertEquals(1, count($cb->definitions));
        $this->assertArrayHasKey('fooServiceId', $cb->definitions);

        /* @var \Symfony\Component\DependencyInjection\Definition $provider */
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
