<?php

namespace Sli\ExpanderBundle\Ext;

/**
 * This interface is used by {@class CompositeMergeContributorsProvider}.
 *
 * Your implementations of {@class ContributorInterface} may optionally implement this interface when you need to achieve
 * a certain sorting when contributions are being merged.
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
interface OrderedContributorInterface
{
    /**
     * @return integer
     */
    public function getOrder();
}
