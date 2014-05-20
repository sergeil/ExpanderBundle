<?php

namespace Sli\ExpanderBundle\Contributing;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
interface ExtensionPointsAwareBundleInterface
{
    /**
     * @return array
     */
    public function getExtensionPointContributions();
} 