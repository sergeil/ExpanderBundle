<?php

namespace Sli\ExpanderBundle\Command;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Sli\ExpanderBundle\Misc\KernelProxy;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ListExtensionPointsCommand extends ContainerAwareCommand
{
    // override
    protected function configure()
    {
        $this
            ->setName('sli:expander:list-extension-points')
            ->setDescription('Shows a lists of available extension-points.')
        ;
    }

    private function cleanUp(KernelInterface $kernel)
    {
        $filesystem = new Filesystem();
        $filesystem->remove($kernel->getCacheDir());
        $filesystem->remove($kernel->getLogDir());
    }

    // override
    public function run(InputInterface $input, OutputInterface $output)
    {
        $kernel = new KernelProxy('dev', true);
        $kernel->boot();

        $this->cleanUp($kernel);

        $rows = array();
        foreach ($kernel->getExtensionCompilerPasses() as $pass) {
            /** @var CompositeContributorsProviderCompilerPass $pass */

            $ep = $pass->getExtensionPoint();

            $rows[] = array(
                $ep ? $ep->getContributionTag() : $pass->getContributorServiceTagName(),
                $ep ? $ep->getId() : $pass->getProviderServiceId(),
            );
        }

        $this->cleanUp($kernel);

        /* @var TableHelper $table */
        $table = $this->getHelperSet()->get('table');
        $table
            ->setHeaders(array('Tag', 'Service ID'))
            ->setRows($rows)
        ;
        $table->render($output);

        $output->writeln(
            '<info>* Tag -- this a tag you need to tag your services with in order to contribute to any given extension point</info>'
        );
        $output->writeln(
            '<info>* Service ID -- this a dynamically built service container ID that can be used to get all extension point contributions</info>'
        );
    }

    static public function clazz()
    {
        return get_called_class();
    }
}