<?php

namespace Sli\ExpanderBundle\DependencyInjection;

use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Sli\ExpanderBundle\Ext\CompositeMergeContributorsProvider;

/**
 * The compiler pass will collect services from the constructor with a defined tag, and create a new service which may
 * be used later to get an aggregated value of their getItems method.
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class CompositeContributorsProviderCompilerPass implements CompilerPassInterface, ExtensionPointAwareCompilerPassInterface
{
    private $contributorServiceTagName;
    private $providerServiceId;
    private $extensionPoint;

    /**
     * @return string
     */
    public function getContributorServiceTagName()
    {
        return $this->contributorServiceTagName;
    }

    /**
     * @return string
     */
    public function getProviderServiceId()
    {
        return $this->providerServiceId;
    }

    /**
     * @deprecated  Use \Sli\ExpanderBundle\Ext\ExtensionPoint::createCompilerPass() method instead!
     *
     * @param string $providerServiceId  This compiler class will contribute a new service with this ID to the
     *                                   container, it will be an instance of the CompositeMergeContributorsProvider class
     * @param null|string $contributorServiceTagName  And the aforementioned instance will collect services from the
     *                                                container which were tagger with this ID
     * @param null|ExtensionPoint $extensionPoint
     */
    public function __construct($providerServiceId, $contributorServiceTagName = null, ExtensionPoint $extensionPoint = null)
    {
        $this->providerServiceId = $providerServiceId;
        $this->contributorServiceTagName = $contributorServiceTagName ?: $providerServiceId;
        $this->extensionPoint = $extensionPoint;
    }

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $providerDef = new Definition(CompositeMergeContributorsProvider::clazz());
        $container->addDefinitions(array(
            $this->getProviderServiceId() => $providerDef
        ));

        $contributors = $container->findTaggedServiceIds($this->getContributorServiceTagName());
        foreach ($contributors as $id => $attributes) {
            $providerDef->addMethodCall('addContributor', array(new Reference($id)));
        }
    }

    /**
     * @return \Sli\ExpanderBundle\Ext\ExtensionPoint
     */
    public function getExtensionPoint()
    {
        return $this->extensionPoint;
    }

    /**
     * @return string
     */
    static public function clazz()
    {
        return get_called_class();
    }
}
