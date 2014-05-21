<?php

namespace Sli\ExpanderBundle\Tests\Unit\Ext;

use Sli\ExpanderBundle\Ext\CompositeMergeContributorsProvider;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Sli\ExpanderBundle\Ext\OrderedContributorInterface;

class MockOrderAwareContributor implements OrderedContributorInterface
{
    public $items = array();
    public $order;

    public function __construct($order = null, $items = array())
    {
        $this->order = $order;
        $this->items = $items;
    }

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
class CompositeMergeContributorsProviderTest extends \PHPUnit_Framework_TestCase
{
    /* @var \Sli\ExpanderBundle\Ext\CompositeMergeContributorsProvider $p */
    private $p;

    public function setUp()
    {
        $this->p = new CompositeMergeContributorsProvider();
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
        $c1 = new MockOrderAwareContributor(100, array('foo'));

        $c2 = \Phake::mock(ContributorInterface::CLAZZ);
        \Phake::when($c2)->getItems()->thenReturn(array('baz'));

        $c3 = new MockOrderAwareContributor(50, array('bar'));

        $this->p->addContributor($c1);
        $this->p->addContributor($c2);
        $this->p->addContributor($c3);

        $this->assertSame(array('bar', 'foo', 'baz'), $this->p->getItems());
    }

    /**
     * @group MPFE-488
     */
    public function testGetItemsWithSameOrder()
    {
        $c1 = new MockOrderAwareContributor(1, array('foo'));

        $c2 = new MockOrderAwareContributor(1, array('bar'));

        $c3 = \Phake::mock(ContributorInterface::CLAZZ);
        \Phake::when($c3)->getItems()->thenReturn(array('baz'));

        $this->p->addContributor($c1);
        $this->p->addContributor($c2);
        $this->p->addContributor($c3);

        $this->assertSame(array('foo', 'bar', 'baz'), $this->p->getItems());
    }
}
