<?php

namespace Sli\ExpanderBundle\Tests\Functional;

use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ExtensionPointsTest extends WebTestCase
{
    public function testHowWellItWorks()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();

        /* @var ContributorInterface $provider */
        $provider = $container->get('sli_expander.dummy_resources_provider');

        $this->assertInstanceOf('Sli\ExpanderBundle\Ext\ContributorInterface', $provider);

        $result = $provider->getItems();

        $this->assertTrue(is_array($result));

        // contributed by \Sli\ExpanderBundle\Tests\Fixtures\Bundles\DummyBundle\Contributions\DummyResourcesProvider
        $this->assertTrue(in_array('foo_resource', $result));
        $this->assertTrue(in_array('bar_resource', $result));

        // contributed indirectly by \Sli\ExpanderBundle\Tests\Fixtures\Bundles\DummyBundle\SliExpanderDummyBundle::getExtensionPointContributions
        $this->assertTrue(in_array('baz_resource', $result));
    }
}
