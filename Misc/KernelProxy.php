<?php

namespace Sli\ExpanderBundle\Misc;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Sli\ExpanderBundle\DependencyInjection\ExtensionPointAwareCompilerPassInterface;
use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This class is not a part of a public API.
 *
 * This kernel class caches used by container ContainerBuilder which allows later to introspect what
 * compiler passed have been used to build a container.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class KernelProxy extends \AppKernel
{
    /** @var ContainerBuilder */
    private $containerBuilder;

    /**
     * @inheritDoc
     */
    protected function buildContainer()
    {
        $containerBuilder = parent::buildContainer();

        $this->containerBuilder = $containerBuilder;

        return $containerBuilder;
    }

    /**
     * @return CompilerPassInterface[]
     */
    public function getExtensionCompilerPasses()
    {
        if (!$this->containerBuilder) {
            throw new \RuntimeException("You haven't yet bootstrapped KernelProxy class!");
        }

        $result = array();

        foreach ($this->containerBuilder->getCompiler()->getPassConfig()->getPasses() as $pass) {
            if ($pass instanceof ExtensionPointAwareCompilerPassInterface) {
                $result[] = $pass;
            }
        }

        return $result;
    }

    /**
     * @param string $id
     *
     * @return null|ExtensionPoint
     */
    public function getExtensionPoint($id)
    {
        /* @var ExtensionPoint $extensionPoint */
        $extensionPoint = null;
        foreach ($this->getExtensionCompilerPasses() as $pass) {
            /** @var CompositeContributorsProviderCompilerPass $pass */

            $iteratedExtensionPoint = $pass->getExtensionPoint();

            if ($iteratedExtensionPoint && $iteratedExtensionPoint->getId() == $id) {
                return $iteratedExtensionPoint;
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getCacheDir()
    {
        return sys_get_temp_dir() . '/sli-kernelproxy/cache';
    }

    /**
     * @inheritDoc
     */
    public function getLogDir()
    {
        return sys_get_temp_dir() . '/sli-kernelproxy/logs';
    }

    public function cleanUp()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getCacheDir());
        $filesystem->remove($this->getLogDir());
    }
}