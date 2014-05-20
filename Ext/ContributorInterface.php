<?php

namespace Sli\ExpanderBundle\Ext;

/**
 * This base interface describes a contract that your application logic may rely upon when it needs to consume
 * contributed to a certain extension points entries.
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
interface ContributorInterface
{
    const CLAZZ = 'Sli\ExpanderBundle\Ext\ContributorInterface';

    /**
     * @return mixed[]
     */
    public function getItems();
}
