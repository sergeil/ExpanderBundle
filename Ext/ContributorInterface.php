<?php

namespace Sli\ExpanderBundle\Ext;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
interface ContributorInterface
{
    const CLAZZ = 'Sli\ExpanderBundle\Ext\ContributorInterface';

    public function getItems();
}
