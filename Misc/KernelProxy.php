<?php

namespace Sli\ExpanderBundle\Misc;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
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
            if ($pass instanceof CompositeContributorsProviderCompilerPass) {
                $result[] = $pass;
            }
        }

        return $result;
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
}