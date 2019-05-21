<?php

namespace Sli\ExpanderBundle;

use Sli\ExpanderBundle\Contributing\ExtensionPointsAwareBundlesCollectorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SliExpanderBundle extends Bundle
{
    /**
     * @var KernelInterface
     */
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
        $container->addCompilerPass(new ExtensionPointsAwareBundlesCollectorCompilerPass($this->kernel));
    }
}
