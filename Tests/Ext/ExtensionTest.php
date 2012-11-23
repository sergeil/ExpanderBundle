<?php

namespace Sli\ExpanderBundle\Tests\Ext;

use Sli\ExpanderBundle\Ext\Extension;
use Sli\ExpanderBundle\Ext\CompilerPass;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testOverall()
    {
        $e = new Extension('foo', 'bar');

        $this->assertSame('foo', $e->getProviderServiceId());
        $this->assertSame('bar', $e->getContributorServiceTagName());

        $cp = $e->createCompilerPass();
        $this->assertInstanceOf(CompilerPass::clazz(), $cp);
        $this->assertSame($e, $cp->getExtension());

        $e2 = new Extension('foo');
        $this->assertSame('foo', $e2->getProviderServiceId());
        $this->assertSame('foo', $e2->getContributorServiceTagName());
    }
}
