<?php

namespace Sli\ExpanderBundle\Generation;

use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Implementations are responsible for creating/updating files that are needed to create a contribution to a given
 * extension point.
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
interface ContributionGeneratorInterface
{
    /**
     * @param BundleInterface $bundle
     * @param ExtensionPoint $ep
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param HelperSet $helperSet
     *
     * @return void
     */
    public function generate(BundleInterface $bundle, ExtensionPoint $ep, InputInterface $input, OutputInterface $output, HelperSet $helperSet);
} 