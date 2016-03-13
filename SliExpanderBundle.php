<?php

namespace Sli\ExpanderBundle;

use Sli\ExpanderBundle\Contributing\ExtensionPointsAwareBundlesCollectorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;

class SliExpanderBundle extends Bundle
{
    private $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel = null)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        if ($this->kernel) {
            $container->addCompilerPass(new ExtensionPointsAwareBundlesCollectorCompilerPass($this->kernel));
        }
    }
}
