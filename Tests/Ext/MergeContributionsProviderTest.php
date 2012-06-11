<?php

namespace Sli\ExpanderBundle\Tests\Ext;

use Sli\ExpanderBundle\Ext\MergeContributionsProvider;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Sli\ExpanderBundle\Ext\OrderedContributorInterface;

class MockOrderAwareContributor implements ContributorInterface, OrderedContributorInterface
{
    public $items = array();
    public $order;

    public function getItems()
    {
        return $this->items;
    }

    public function getOrder()
    {
        return $this->order;
    }
}

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class MergeContributionsProviderTest extends \PHPUnit_Framework_TestCase
{
    private $p;

    public function setUp()
    {
        $this->p = new MergeContributionsProvider();
    }

    public function testAddContributorAndThenGetContributors()
    {
        $c1 = $this->getMock(ContributorInterface::CLAZZ);
        $c2 = $this->getMock(ContributorInterface::CLAZZ);

        $this->p->addContributor($c1);
        $this->p->addContributor($c2);

        $this->assertSame(array($c1, $c2), $this->p->getContributors());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetItemsWhenContributorReturnsNonArray()
    {
        $c1 = $this->getMock(ContributorInterface::CLAZZ);
        $c1->expects($this->any())
           ->method('getItems')
           ->will($this->returnValue('foo'));

        $this->p->addContributor($c1);

        $this->p->getItems();
    }

    public function testGetItems()
    {
        $c1 = $this->getMock(ContributorInterface::CLAZZ);
        $c1->expects($this->any())
           ->method('getItems')
           ->will($this->returnValue(array('foo1', 'foo2')));

        $c2 = $this->getMock(ContributorInterface::CLAZZ);
        $c2->expects($this->any())
           ->method('getItems')
           ->will($this->returnValue(array('bar1', 'bar2')));

        $this->p->addContributor($c1);
        $this->p->addContributor($c2);

        $result = $this->p->getItems();
        $this->assertTrue(is_array($result));
    }

    public function testGetItemsWithOrder()
    {
        $c1 = new MockOrderAwareContributor();
        $c1->order = 100;
        $c1->items = array('foo');

        $c2 = $this->getMock(ContributorInterface::CLAZZ);
        $c2->expects($this->any())
           ->method('getItems')
           ->will($this->returnValue(array('baz')));

        $c3 = new MockOrderAwareContributor();
        $c3->order = 50;
        $c3->items = array('bar');

        $this->p->addContributor($c1);
        $this->p->addContributor($c2);
        $this->p->addContributor($c3);

        $this->assertSame(array('bar', 'foo', 'baz'), $this->p->getItems());
    }
}
