<?php

namespace Sli\ExpanderBundle\Tests\Unit\Contributing;

use Sli\ExpanderBundle\Contributing\BundleContributorAdapter;
use Sli\ExpanderBundle\Contributing\ExtensionPointsAwareBundleInterface;
use Sli\ExpanderBundle\Tests\Unit\FooDummyBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class BundleContributorAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetItems()
    {
        $extensionPointName = 'foo_extension_point';

        $bundleName = 'FooDummyBundle';

        $bundle = new FooDummyBundle();
        $bundle->map[$extensionPointName] = array('foo', 'bar');

        $kernel = \Phake::mock('Symfony\Component\HttpKernel\KernelInterface');
        \Phake::when($kernel)->getBundle($bundleName)->thenReturn($bundle);

        $a = new BundleContributorAdapter($kernel, $bundleName, $extensionPointName);

        $result = $a->getItems();

        $this->assertTrue(is_array($result));
        $this->assertSame($result, array('foo', 'bar'));

        // ---

        $a = new BundleContributorAdapter($kernel, $bundleName, $extensionPointName);
        $bundle->map = null;

        $this->assertSame(array(), $a->getItems());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetItemsWithVanillaBundle()
    {
        $extensionPointName = 'foo_extension_point';

        $bundleName = 'FooDummyBundle';

        $bundle = \Phake::mock('Symfony\Component\HttpKernel\Bundle\BundleInterface');

        $kernel = \Phake::mock('Symfony\Component\HttpKernel\KernelInterface');
        \Phake::when($kernel)->getBundle($bundleName)->thenReturn($bundle);

        $a = new BundleContributorAdapter($kernel, $bundleName, $extensionPointName);

        $result = $a->getItems();
    }
}