<?php

namespace Sli\ExpanderBundle\Ext;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class SimpleContributor implements ContributorInterface
{
    private $items = array();

    /**
     * @param mixed[] $items
     */
    public function __construct(array $items = array())
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    /**
     * @param mixed $item
     */
    public function addItem($item)
    {
        $this->items[spl_object_hash($item)] = $item;
    }

    /**
     * @return mixed[]
     */
    public function getItems()
    {
        return $this->items;
    }
}
