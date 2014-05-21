<?php

namespace Sli\ExpanderBundle\Ext;

use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */ 
class ExtensionPoint 
{
    /* @var string */
    private $id;
    /* @var string */
    private $contributionTag;
    /* @var string */
    private $category;
    /* @var string */
    private $description;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return CompilerPassInterface
     */
    public function createCompilerPass()
    {
        return new CompositeContributorsProviderCompilerPass($this->id, $this->contributionTag, $this);
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $contributionTag
     */
    public function setContributionTag($contributionTag)
    {
        $this->contributionTag = $contributionTag;
    }

    /**
     * @return string
     */
    public function getContributionTag()
    {
        if (!$this->contributionTag) {
            return $this->id;
        }

        return $this->contributionTag;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}