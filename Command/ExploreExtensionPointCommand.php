<?php

namespace Sli\ExpanderBundle\Command;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Sli\ExpanderBundle\Misc\KernelProxy;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ExploreExtensionPointCommand extends ContainerAwareCommand
{
    // override
    protected function configure()
    {
        $this
            ->setName('sli:expander:explore-extension-point')
            ->addArgument('id', InputArgument::REQUIRED)
        ;
    }

    // override
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel = new KernelProxy('dev', true);
        $kernel->boot();

        $kernel->cleanUp();

        $idArg = $input->getArgument('id');

        /* @var ExtensionPoint $extensionPoint */
        $extensionPoint = null;
        foreach ($kernel->getExtensionCompilerPasses() as $pass) {
            /** @var CompositeContributorsProviderCompilerPass $pass */

            $iteratedExtensionPoint = $pass->getExtensionPoint();

            if ($iteratedExtensionPoint && $iteratedExtensionPoint->getId() == $idArg) {
                $extensionPoint = $iteratedExtensionPoint;
            }
        }

        $kernel->cleanUp();

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