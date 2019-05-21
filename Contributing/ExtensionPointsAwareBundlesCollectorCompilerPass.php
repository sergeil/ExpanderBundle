<?php

namespace Sli\ExpanderBundle\Contributing;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * For every extension point contributed by bundles which implement ExtensionPointsAwareBundleInterface this
 * compiles pass will dynamically contribute a provider class to DI container so later you can see a container
 * dump file to find all contributions for a certain extension-point.
 *
 * This compiler pass will be automatically registered if when you added {@class \Sli\ExpanderBundle\SliExpanderBundle}
 * to your app kernel you provided a link to kernel as its first argument.
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ExtensionPointsAwareBundlesCollectorCompilerPass implements CompilerPassInterface
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
     * @param BundleInterface $bundle
     * @param string          $extensionPointName
     * @return string
     */
    private function createServiceName(BundleInterface $bundle, $extensionPointName)
    {
        return strtolower($bundle->getName().'.dynamic_contribution.'.str_replace('.', '_', $extensionPointName));
    }

    /**
     * @param ContainerBuilder $container
     * @return ExtensionPointsAwareBundleInterface[]
     */
    private function getExtensionPointsAwareBundles(ContainerBuilder $container)
    {
        $bundles = array();
        if ($this->kernel) {
            foreach ($this->kernel->getBundles() as $bundle) {
                if ($bundle instanceof ExtensionPointsAwareBundleInterface) {
                    $bundles[] = $bundle;
                }
            }

        } else {
            foreach ($container->getParameter('kernel.bundles') as $bundleName) {
                if (is_subclass_of($bundleName, ExtensionPointsAwareBundleInterface::class)) {
                    $bundles[] = new $bundleName;
                }
            }
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        /* @var BundleInterface $bundle */
        foreach ($this->getExtensionPointsAwareBundles($container) as $bundle) {
            foreach ($bundle->getExtensionPointContributions() as $extensionPointName => $contributions) {
                $serviceName = $this->createServiceName($bundle, $extensionPointName);

                if ($container->hasDefinition($serviceName)) {
                    throw new \RuntimeException(
                        "Unable to dynamically register a new service with ID '$serviceName', this ID is already in use."
                    );
                }

                $definitionArgs = array(
                    new Reference('kernel'),
                    $bundle->getName(),
                    $extensionPointName,
                );
                $definition = new Definition(BundleContributorAdapter::clazz(), $definitionArgs);
                $definition->addTag($extensionPointName);

                $container->setDefinition($serviceName, $definition);
            }
        }
    }
}
