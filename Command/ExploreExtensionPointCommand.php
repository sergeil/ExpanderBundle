<?php

namespace Sli\ExpanderBundle\Command;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Sli\ExpanderBundle\Misc\KernelProxy;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ExploreExtensionPointCommand extends AbstractCommand
{
    // override
    protected function configure()
    {
        $this
            ->setName('sli:expander:explore-extension-point')
            ->addArgument('id', InputArgument::REQUIRED)
            ->setDescription('Provides detailed information about an extension point.')
        ;
    }

    // override
    protected function doExecute(KernelProxy $kernelProxy, InputInterface $input, OutputInterface $output)
    {
        $idArg = $input->getArgument('id');

        /* @var ExtensionPoint $extensionPoint */
        $extensionPoint = null;
        foreach ($kernelProxy->getExtensionCompilerPasses() as $pass) {
            /* @var CompositeContributorsProviderCompilerPass $pass */

            $iteratedExtensionPoint = $pass->getExtensionPoint();

            if ($iteratedExtensionPoint && $iteratedExtensionPoint->getId() == $idArg) {
                $extensionPoint = $iteratedExtensionPoint;
            }
        }

        if (!$extensionPoint) {
            throw new \RuntimeException("Extension point with ID '$idArg' is not found.");
        }

        $output->writeln('<info>ID:</info>');
        $output->writeln($extensionPoint->getId());

        $output->writeln('<info>Category:</info>');
        $output->writeln($extensionPoint->getCategory() ? $extensionPoint->getCategory() : '-');

        $output->writeln('<info>Batch contribution tag:</info>');
        $output->writeln($extensionPoint->getBatchContributionTag());

        $output->writeln('<info>Single contribution tag:</info>');
        $output->writeln('-');

        $output->writeln('<info>Description:</info>');
        $output->writeln($extensionPoint->getDescription() ? $extensionPoint->getDescription() : '-');

        $output->writeln('<info>Detailed description:</info>');
        $output->writeln($extensionPoint->isDetailedDescriptionAvailable() ? $extensionPoint->getDetailedDescription() : '-');
    }
}
