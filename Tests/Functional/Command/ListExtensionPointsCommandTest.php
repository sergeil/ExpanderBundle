<?php

namespace Sli\ExpanderBundle\Tests\Functional\Command;

use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Sli\ExpanderBundle\Command\ListExtensionPointsCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ListExtensionPointsCommandTest extends FunctionalTestCase
{
    public function testExecute()
    {
        $app = new Application(self::$kernel);
        $app->add(new ListExtensionPointsCommand());

        $command = $app->find('sli:expander:list-extension-points');

        $this->assertInstanceOf(ListExtensionPointsCommand::clazz(), $command);

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), '--skip-question' => true));

        $this->assertRegExp('/sli_expander.dummy_resources/', $commandTester->getDisplay());
        $this->assertRegExp('/sli_expander.blah_resources/', $commandTester->getDisplay());

        // ---

        // with filter specified:
        $commandTester->execute(array('command' => $command->getName(), 'id-filter' => 'blah', '--skip-question' => true));

        $this->assertRegExp('/sli_expander.blah_resources/', $commandTester->getDisplay());
        $this->assertNotRegExp('/sli_expander.dummy_resources/', $commandTester->getDisplay());
    }
} 