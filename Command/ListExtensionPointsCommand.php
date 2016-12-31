<?php

namespace Sli\ExpanderBundle\Command;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Sli\ExpanderBundle\Misc\KernelProxy;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author    Sergei Lissovski <sergei.lissovski@gmail.com>
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
            /* @var CompositeContributorsProviderCompilerPass $pass */

            $ep = $pass->getExtensionPoint();

            if (!$ep || (null !== $idFilter && false === strpos($ep->getId(), $idFilter))) {
                continue;
            }

            $rows[] = array(
                $i + 1,
                $ep->getId(),
                $ep->isDetailedDescriptionAvailable() ? 'Yes' : 'No',
                $ep ? $ep->getDescription() : '',
            );

            ++$i;
        }

        $isSymfony2 = substr(Kernel::VERSION, 0, 1) == '2';

        $table = null;
        if ($isSymfony2) {
            /* @var \Symfony\Component\Console\Helper\TableHelper $table */
            $table = $this->getHelperSet()->get('table');
        } else {
            $table = new \Symfony\Component\Console\Helper\Table($output);
        }

        $table
            ->setHeaders(array('#', 'Name', 'Docs', 'Description'))
            ->setRows($rows)
        ;
        $table->render($output);

        if (!$input->getOption('skip-question')) {
            // sf2: http://symfony.com/doc/2.5/components/console/helpers/dialoghelper.html
            // sf3: http://symfony.com/doc/3.0/components/console/helpers/questionhelper.html
            $helper = $this->getHelper($isSymfony2 ? 'dialog' : 'question');

            $answer = null;
            if ($isSymfony2) {
                $answer = $helper->ask($output, 'Extension point # you want to see detailed documentation for: ');
            } else {
                $question = new \Symfony\Component\Console\Question\Question('Extension point # you want to see detailed documentation for: ');

                $answer = $helper->ask($input, $output, $question);
            }

            $extensionPointId = null;

            if (null !== $answer) {
                $extensionPointId = $rows[$answer - 1][1];
                $this->getApplication()->run(new StringInput('sli:expander:explore-extension-point '.$extensionPointId));
            }

            $question = 'Would you like to create a contribution to this extension-point right away ? ';
            $output->writeln(str_repeat('-', strlen($question)));

            $answer = null;
            if ($isSymfony2) {
                $answer = $helper->askConfirmation($output, "<info>$question</info>");
            } else {
                $confirmationQuestion = new \Symfony\Component\Console\Question\ConfirmationQuestion($question);

                $answer = $helper->ask($confirmationQuestion);
            }

            if ($answer) {
                $output->writeln('');
                $this->getApplication()->run(new StringInput('sli:expander:contribute '.$extensionPointId));
            }
        }
    }

    public static function clazz()
    {
        return get_called_class();
    }
}
