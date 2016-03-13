<?php

namespace Sli\ExpanderBundle\Contributing;

/**
 * You bundle class may optionally implement this interface if you want to leverage a simplified way how to contribute
 * to extension-points.
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
interface ExtensionPointsAwareBundleInterface
{
    /**
     * Must return an array where keys are extension point names and values are another arrays containing
     * entries you want to contribute to those extension-points.
     *
     * @return array
     */
    public function getExtensionPointContributions();
}
