<?php

namespace Sli\ExpanderBundle\Tests\Unit\Generation;

use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Sli\ExpanderBundle\Generation\StandardContributionGenerator;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class StandardContributionGeneratorTest extends \PHPUnit_Framework_TestCase
{
    private $dir;
    private $mocks = array();

    public function setUp()
    {
        $this->dir = sys_get_temp_dir().'/sli-expander-gentest';
        if (!file_exists($this->dir)) {
            $fs = new Filesystem();

            $fs->mkdir(array($this->dir, $this->dir.'/Resources/config'));

            $servicesXmlContents = <<<XML
<?xml version="1.0" ?>

<container xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>
    </services>
</container>
XML;
            file_put_contents($this->dir.'/Resources/config/services.xml', $servicesXmlContents);
        }

        $bundle = \Phake::mock('Symfony\Component\HttpKernel\Bundle\BundleInterface');
        $ep = \Phake::mock(ExtensionPoint::clazz());
        $input = \Phake::mock('Symfony\Component\Console\Input\InputInterface');
        $output = \Phake::mock('Symfony\Component\Console\Output\OutputInterface');
        $helperSet = \Phake::mock('Symfony\Component\Console\Helper\HelperSet');

        \Phake
            ::when($bundle)
            ->getPath()
            ->thenReturn($this->dir)
        ;
        \Phake
            ::when($bundle)
            ->getName()
            ->thenReturn('SliExpanderDummyBundle')
        ;
        \Phake
            ::when($bundle)
            ->getNamespace()
            ->thenReturn('FooNamespace')
        ;

        \Phake::when($ep)
            ->getBatchContributionTag()
            ->thenReturn('blah_foo_tag')
        ;

        $this->mocks = array(
            $bundle, $ep, $input, $output, $helperSet,
        );
    }

    public function tearDown()
    {
        $fs = new Filesystem();
        $fs->remove(array($this->dir));
    }

    public function testGenerate()
    {
        $g = new StandardContributionGenerator(array('className' => 'FooContribution'));

        $g->generate(
            $this->mocks[0], $this->mocks[1], $this->mocks[2], $this->mocks[3], $this->mocks[4]
        );

        $this->assertTrue(file_exists($this->dir.'/Contributions/FooContribution.php'));
        $classContents = file_get_contents($this->dir.'/Contributions/FooContribution.php');
        $this->assertTrue(false !== strpos($classContents, 'namespace FooNamespace\\Contributions'));

        $this->assertTrue(file_exists($this->dir.'/Resources/config/services.xml'));
        $servicesXmlContents = file_get_contents($this->dir.'/Resources/config/services.xml');
        $this->assertTrue(false !== strpos($servicesXmlContents, 'class="FooNamespace\\Contributions\\FooContribution"'));
        $this->assertTrue(false !== strpos($servicesXmlContents, 'id="sli_expander_dummy.contributions.foo_contribution"'));
        $this->assertTrue(false !== strpos($servicesXmlContents, '<tag name="blah_foo_tag" />'));
    }

    public function testGenerateWithQuestion()
    {
        $g = new StandardContributionGenerator(array());

        $dialogHelper = \Phake::mock('Symfony\Component\Console\Helper\DialogHelper');
        \Phake
            ::when($dialogHelper)
            ->ask(\Phake::anyParameters())
            ->thenReturn('BarContribution')
        ;

        $helperSet = $this->mocks[4];
        \Phake
            ::when($helperSet)
            ->get('dialog')
            ->thenReturn($dialogHelper)
        ;

        $g->generate(
            $this->mocks[0], $this->mocks[1], $this->mocks[2], $this->mocks[3], $this->mocks[4]
        );

        $this->assertTrue(file_exists($this->dir.'/Resources/config/services.xml'));
        $servicesXmlContents = file_get_contents($this->dir.'/Resources/config/services.xml');
        $this->assertTrue(false !== strpos($servicesXmlContents, 'class="FooNamespace\\Contributions\\BarContribution"'));
        $this->assertTrue(false !== strpos($servicesXmlContents, 'id="sli_expander_dummy.contributions.bar_contribution"'));
        $this->assertTrue(false !== strpos($servicesXmlContents, '<tag name="blah_foo_tag" />'));
    }

    public function testIsValidClassName()
    {
        $g = new StandardContributionGenerator(array());

        $this->assertFalse($g->isValidClassName(''));
        $this->assertFalse($g->isValidClassName('Foo bar')); // there's a space
        $this->assertTrue($g->isValidClassName('Foobar'));
    }
}
