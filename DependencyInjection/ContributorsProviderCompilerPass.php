<?php

namespace Sli\ExpanderBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Sli\ExpanderBundle\Ext\CompositeMergeContributorsProvider;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ContributorsProviderCompilerPass implements CompilerPassInterface
{
    function process(ContainerBuilder $container)
    {
        // TODO
    }
}
