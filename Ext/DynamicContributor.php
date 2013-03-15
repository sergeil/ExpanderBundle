<?php

namespace Sli\ExpanderBundle\Ext;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class DynamicContributor implements ContributorInterface
{
    private $items = array();

    public function __construct(array $items = array())
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    public function addItem($item)
    {
        $this->items[spl_object_hash($item)] = $item;
    }

    public function getItems()
    {
        return $this->items;
    }
}
