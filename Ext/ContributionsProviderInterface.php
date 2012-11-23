<?php

namespace Sli\ExpanderBundle\Ext;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
interface ContributionsProviderInterface
{
    /**
     * @param ContributorInterface $contributor
     */
    public function addContributor(ContributorInterface $contributor);

    /**
     * @return \Sli\ExpanderBundle\Ext\ContributorInterface
     */
    public function getContributors();

    /**
     * @return array
     */
    public function getItems();
}
