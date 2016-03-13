<?php

namespace Sli\ExpanderBundle\Tests\Fixtures\Bundles\DummyBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class DummyResourcesProvider implements ContributorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return array(
            'foo_resource',
            'bar_resource',
        );
    }
}
