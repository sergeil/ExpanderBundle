<?php

namespace Sli\ExpanderBundle\Ext;

/**
 * This provider know how to deal with with aggregated providers that happen to implement OrderedContributorInterface
 * interface. If there are several providers that have the same order specified, then LIFO method is used to resolve
 * the best one. If some of providers do no implement this interface, then they will be appended to the end of all
 * providers ( when #getItems() method is invoked ).
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ChainMergeContributorsProvider implements ChainContributorsProviderInterface
{
    private $contributors = array();

    /**
     * @inheritDoc
     */
    public function addContributor(ContributorInterface $contributor)
    {
        $this->contributors[] = $contributor;
    }

    /**
     * @inheritDoc
     */
    public function getContributors()
    {
        return $this->contributors;
    }

    /**
     * @inheritDoc
     * @throws \RuntimeException
     */
    public function getItems()
    {
        $orderedContributors = array();
        $plainContributors = array();
        foreach ($this->contributors as $contributor) {
            if ($contributor instanceof OrderedContributorInterface) {
                $orderedContributors[] = $contributor;
            } else {
                $plainContributors[] = $contributor;
            }
        }

        // @ is required to avoid having errors thrown by some versions of PHP
        @usort($orderedContributors, function(OrderedContributorInterface $a, OrderedContributorInterface $b) {
            if ($a->getOrder() == $b->getOrder()) {
                return 1;
            }

            return $b->getOrder() < $a->getOrder() ? 1 : -1;
        });

        $contributors = array_merge($orderedContributors, $plainContributors);

        $result = array();
        foreach ($contributors as $contributor) {
            /* @var ContributorInterface $contributor */
            $contributorResult = $contributor->getItems();
            if (!is_array($contributorResult)) {
                throw new \RuntimeException(
                    sprintf('Contributor %s::getItems() must always return an array!', get_class($contributor))
                );
            }

            $result = array_merge($result, $contributorResult);
        }

        return $result;
    }

    static public function clazz()
    {
        return get_called_class();
    }
}
