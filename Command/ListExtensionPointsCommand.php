<?php

namespace Sli\ExpanderBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Sli\ExpanderBundle\Misc\KernelProxy;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

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
            ->addArgument('id-filter', null, 'Allows to filter displayed extension points')
            ->addOption(
                'skip-question', null, null,
                'If given then command will not ask a user to type in command # to display its detailed description.'
            )
        ;
    }

    // override
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel = new KernelProxy('dev', true);
        $kernel->boot();

        $kernel->cleanUp();

        $i = 0;

        $idFilter = $input->getArgument('id-filter');

        $rows = array();
        foreach (array_values($kernel->getExtensionCompilerPasses()) as $pass) {
            /** @var CompositeContributorsProviderCompilerPass $pass */

            $ep = $pass->getExtensionPoint();

            if (!$ep || (null !== $idFilter && false === strpos($ep->getId(), $idFilter))) {
                continue;
            }

            $rows[] = array(
                $i + 1,
                $ep->getId(),
                $ep->isDetailedDescriptionAvailable() ? 'Yes' : 'No',
                $ep ? $ep->getDescription() : ''
            );

            $i++;
        }

        $kernel->cleanUp();

        /* @var TableHelper $table */
        $table = $this->getHelperSet()->get('table');
        $table
            ->setHeaders(array('#', 'Name', 'Docs', 'Description'))
            ->setRows($rows)
        ;
        $table->render($output);

        if (!$input->getOption('skip-question')) {
            /* @var DialogHelper $dialogHelper */
            $dialogHelper = $this->getHelper('dialog');
            $answer = $dialogHelper->ask($output, 'Extension point # you want to see detailed documentation for: ');
            if (null !== $answer) {
                $id = $rows[$answer-1][1];
                $this->getApplication()->run(new StringInput('sli:expander:explore-extension-point ' . $id));
            }
        }
    }

    static public function clazz()
    {
        return get_called_class();
    }
}