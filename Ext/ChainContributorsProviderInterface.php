<?php

namespace Sli\ExpanderBundle\Ext;

/**
 * Implementations of this interface are to be capable of merging contributed items provided by other
 * contributors in some way.
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
interface ChainContributorsProviderInterface extends ContributorInterface
{
    /**
     * @param ContributorInterface $contributor
     */
    public function addContributor(ContributorInterface $contributor);

    /**
     * @return ContributorInterface[]
     */
    public function getContributors();
}
