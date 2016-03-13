<?php

namespace Sli\ExpanderBundle\Tests\Unit\Ext;

use Sli\ExpanderBundle\Ext\SimpleContributor;

/**
 * @copyright 2012 Modera Foundation
 * @author Sergei Lissovski <sergei.lissovski@modera.net>
 */
class DynamicContributorTest extends \PHPUnit_Framework_TestCase
{
    public function testThemAll()
    {
        $c1 = new \stdClass();
        $c2 = $c1;

        $dc = new SimpleContributor(array($c1, $c2));
        $this->assertEquals(1, count($dc->getItems()));

        $dc->addItem($c1);
        $this->assertEquals(1, count($dc->getItems()));

        $dc->addItem(new \stdClass());
        $this->assertEquals(2, count($dc->getItems()));
    }
}
