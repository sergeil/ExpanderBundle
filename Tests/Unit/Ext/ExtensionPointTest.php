<?php

namespace Sli\ExpanderBundle\Tests\Unit\Ext;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Sli\ExpanderBundle\Ext\ExtensionPoint;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ExtensionPointTest extends \PHPUnit_Framework_TestCase
{
    /* @var ExtensionPoint $ep */
    private $ep;

    public function setUp()
    {
        $this->ep = new ExtensionPoint('foo.blah.cities');
    }

    public function testCreateCompilerPass()
    {
        /* @var CompositeContributorsProviderCompilerPass $cp */
        $cp = $this->ep->createCompilerPass();

        $this->assertInstanceOf(CompositeContributorsProviderCompilerPass::clazz(), $cp);
        $this->assertEquals('foo.blah.cities_provider', $cp->getProviderServiceId());
        $this->assertEquals('foo.blah.cities_provider', $cp->getContributorServiceTagName());
    }

    public function testMethodChaining()
    {
        $result = $this->ep->setDescription('desc');

        $this->assertInstanceOf(ExtensionPoint::clazz(), $result);

        $result = $this->ep->setCategory('cat');

        $this->assertInstanceOf(ExtensionPoint::clazz(), $result);

        $result = $this->ep->setSingleContributionTag('ct');

        $this->assertInstanceOf(ExtensionPoint::clazz(), $result);

        $result = $this->ep->setDetailedDescription('foo');

        $this->assertInstanceOf(ExtensionPoint::clazz(), $result);
    }

    public function testIsDetailedDescriptionAvailable()
    {
        $this->assertFalse($this->ep->isDetailedDescriptionAvailable());

        $this->ep->setDetailedDescription('fooblah');

        $this->assertTrue($this->ep->isDetailedDescriptionAvailable());
    }
} 