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
class ListExtensionPointsCommand extends AbstractCommand
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
    protected function doExecute(KernelProxy $kernelProxy, InputInterface $input, OutputInterface $output)
    {
        $this->getApplication()->setAutoExit(false);

        $i = 0;

        $idFilter = $input->getArgument('id-filter');

        $rows = array();
        foreach (array_values($kernelProxy->getExtensionCompilerPasses()) as $pass) {
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

            $extensionPointId = null;

            if (null !== $answer) {
                $extensionPointId = $rows[$answer-1][1];
                $this->getApplication()->run(new StringInput('sli:expander:explore-extension-point ' . $extensionPointId));
            }

            $question = 'Would you like to create a contribution to this extension-point right away ? ';
            $output->writeln(str_repeat('-', strlen($question)));
            $answer = $dialogHelper->askConfirmation($output, "<info>$question</info>");

            if ($answer) {
                $output->writeln('');
                $this->getApplication()->run(new StringInput('sli:expander:contribute ' . $extensionPointId));
            }
        }
    }

    static public function clazz()
    {
        return get_called_class();
    }
}