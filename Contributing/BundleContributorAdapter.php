<?php

namespace Sli\ExpanderBundle\Contributing;

use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This class is not part of a public API.
 *
 * This class is used internally by {@class ExtensionPointsAwareBundlesCollectorCompilerPass}.
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class BundleContributorAdapter implements ContributorInterface
{
    private $kernel;
    private $bundleName;
    private $extensionPointName;

    /**
     * @param HttpKernelInterface $kernel
     * @param string $bundleName
     * @param string $extensionPointName
     */
    public function __construct(KernelInterface $kernel, $bundleName, $extensionPointName)
    {
        $this->kernel = $kernel;
        $this->bundleName = $bundleName;
        $this->extensionPointName = $extensionPointName;
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        $bundle = $this->kernel->getBundle($this->bundleName);
        if ($bundle instanceof ExtensionPointsAwareBundleInterface) {
            $contributions = $bundle->getExtensionPointContributions();

            if (   is_array($contributions) && isset($contributions[$this->extensionPointName])
                && is_array($contributions[$this->extensionPointName])) {

                return $contributions[$this->extensionPointName];
            }
        } else {
            throw new \InvalidArgumentException(sprintf(
                "Bundle '%s' doesn't implement ExtensionPointsAwareBundleInterface interface",
                get_class($bundle)
            ));
        }

        return array();
    }

    static public function clazz()
    {
        return get_called_class();
    }
} 