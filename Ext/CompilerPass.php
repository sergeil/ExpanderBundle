<?php

namespace Sli\ExpanderBundle\Ext;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

/**
 * The compiler pass will collect services from the constructor with a defined tag, and create
 * a new service which may be used later to get an aggregated value of their getItems method.
 * Instance of the Extension class passed to the constructor is used as follows:
 * - $providerServiceId -- This compiler class will contribute a new service with this ID to the container,
 *                         it will be an instance of the MergeContributionsProvider class
 * - contributorServiceTagName -- And the aforementioned instance will collect services from the container
 *                                which were tagger with this ID
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class CompilerPass implements CompilerPassInterface
{
//    private $extension;

    private $contributorServiceTagName;
    private $providerServiceId;

    public function getContributorServiceTagName()
    {
        return $this->contributorServiceTagName;
    }

    public function getProviderServiceId()
    {
        return $this->providerServiceId;
    }

//    /**
//     * @return Extension
//     */
//    public function getExtension()
//    {
//        return $this->extension;
//    }

//    public function __construct(Extension $extension)
//    {
//        $this->extension = $extension;
//    }

    public function __construct($providerServiceId, $contributorServiceTagName = null)
    {
        $this->providerServiceId = $providerServiceId;
        $this->contributorServiceTagName = $contributorServiceTagName ?: $providerServiceId;
    }

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $providerDef = new Definition(MergeContributionsProvider::clazz());
        $container->addDefinitions(array(
            $this->getProviderServiceId() => $providerDef
        ));

        $contributors = $container->findTaggedServiceIds($this->getContributorServiceTagName());
        foreach ($contributors as $id => $attributes) {
            $providerDef->addMethodCall('addContributor', array(new Reference($id)));
        }
    }

    /**
     * @return string
     */
    static public function clazz()
    {
        return get_called_class();
    }
}
