<?php

namespace Sli\ExpanderBundle\Tests\Unit\Command;

use Sli\ExpanderBundle\Command\ContributeCommand;
use Sli\ExpanderBundle\Misc\KernelProxy;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Here we are testing only a "happy path" scenario.
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ContributeCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $bundles = array(
            \Phake::mock('Symfony\Component\HttpKernel\Bundle\BundleInterface'),
        );

        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');

        $kernel = \Phake::mock('Symfony\Component\HttpKernel\KernelInterface');
        \Phake::when($kernel)
            ->getContainer()
            ->thenReturn($container)
        ;

        \Phake::when($kernel)
            ->getBundles()
            ->thenReturn($bundles)
        ;

        $app = new Application($kernel);
        $app->add(new ContributeCommand());

        /* @var ContributeCommand $command */
        $command = $app->find('sli:expander:contribute');

        $this->assertInstanceOf(ContributeCommand::clazz(), $command);

        $generator = \Phake::mock('Sli\ExpanderBundle\Generation\ContributionGeneratorInterface');

        $extensionPoint = \Phake::mock('Sli\ExpanderBundle\Ext\ExtensionPoint');
        $epId = 'foo-ep-id';
        \Phake::when($extensionPoint)
            ->getContributionGenerator()
            ->thenReturn($generator)
        ;

        $kernelProxy = \Phake::mock(KernelProxy::clazz());
        $command->kernelProxy = $kernelProxy;
        \Phake::when($kernelProxy)
            ->getExtensionPoint($epId)
            ->thenReturn($extensionPoint)
        ;
        \Phake::when($kernelProxy)
            ->getBundles()
            ->thenReturn($bundles)
        ;

        $tester = new CommandTester($command);

        $tester->execute(array(
            'command' => 'sli:expander:contribute',
            'id' => $epId,
        ));

        \Phake::verify($generator)->generate(
            \Phake::capture($targetBundle),
            \Phake::capture($targetExtensionPoint),
            \Phake::capture($targetInput),
            \Phake::capture($targetOutput),
            \Phake::capture($targetHelperSet)
        );

        $this->assertSame($bundles[0], $targetBundle);
        $this->assertSame($extensionPoint, $targetExtensionPoint);
        $this->assertInstanceOf('Symfony\Component\Console\Input\InputInterface', $targetInput);
        $this->assertInstanceOf('Symfony\Component\Console\Output\OutputInterface', $targetOutput);
        $this->assertInstanceOf('Symfony\Component\Console\Helper\HelperSet', $targetHelperSet);
    }
}
