<?php

namespace Sli\ExpanderBundle\Command;

use Sli\ExpanderBundle\Misc\KernelProxy;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ContributeCommand extends ContainerAwareCommand
{
    // override
    protected function configure()
    {
        $this
            ->setName('sli:expander:contribute')
            ->addArgument('id', InputArgument::REQUIRED, 'Extension point ID')
            ->addArgument('bundle-filter')
            ->setDescription('Allow to create a contribute for an extension point.')
        ;
    }

    // override
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel = new KernelProxy('dev', true);
        $kernel->boot();

        $kernel->cleanUp();

        $idArg = $input->getArgument('id');
        $bundleFilterArg = $input->getArgument('bundle-filter');

        $extensionPoint = $kernel->getExtensionPoint($idArg);
        if (!$extensionPoint) {
            throw new \RuntimeException("Unable to find an extension point with ID '$idArg'.");
        }

        $bundlesToIterate = array();
        if (null !== $bundleFilterArg) {
            foreach ($kernel->getBundles() as $bundle) {
                if (false !== strpos($bundle->getName(), $bundleFilterArg)) {
                    $bundlesToIterate[] = $bundle;
                }
            }
        } else {
            $bundlesToIterate = $kernel->getBundles();
        }

        $bundleToGenerateTo = null;

        if (count($bundlesToIterate) == 1) {
            $bundleToGenerateTo = $bundlesToIterate[0];
        } else if (count($bundlesToIterate) > 1) {
            /* @var Bundle[] $bundles */
            $bundles = array();
            $rows = array();
            foreach ($bundlesToIterate as $bundle) {
                // ignoring symfony bundles
                if (substr($bundle->getNamespace(), 0, strlen('Symfony')) == 'Symfony') {
                    continue;
                }

                $index = count($rows) + 1;

                $bundles[$index] = $bundle;

                $rows[] = array(
                    $index, $bundle->getName(), $bundle->getPath()
                );
            }

            /* @var TableHelper $table */
            $table = $this->getHelperSet()->get('table');
            $table
                ->setHeaders(array('#', 'Name', 'Location'))
                ->setRows($rows)
            ;
            $table->render($output);

            /* @var DialogHelper $dialog */
            $dialog = $this->getHelper('dialog');

            $bundleIndex = $dialog->ask($output, '<info>Please specify bundle # you want to contribute to:</info> ');
            if (!isset($bundles[$bundleIndex])) {
                throw new \RuntimeException("Unable to find a bundle with given index.");
            }

            $bundleToGenerateTo = $bundles[$bundleIndex];
        } else {
            throw new \RuntimeException("Unable to find any bundles which match given '$bundleFilterArg' filter.");
        }

        $generator = $extensionPoint->getContributionGenerator();
        if (!$generator) {
            throw new \RuntimeException(sprintf(
                "It turns out that extension point '%s' doesn't support contribution generation.",
                $extensionPoint->getId()
            ));
        }

        $generator->generate($bundleToGenerateTo, $extensionPoint, $input, $output, $this->getHelperSet());

        $kernel->cleanUp();
    }
} 