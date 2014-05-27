<?php

namespace Sli\ExpanderBundle\Ext;

use Doctrine\Common\Util\Inflector;
use Sli\ExpanderBundle\DependencyInjection\CompositeContributorsProviderCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ExtensionPoint
{
    /* @var string */
    private $id;
    /* @var string */
    private $singleContributionTag;
    /* @var string */
    private $batchContributionTag;
    /* @var string */
    private $category;
    /* @var string */
    private $description;
    /* @var string */
    private $detailedDescription;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->batchContributionTag = $id . '_provider';
    }

    /**
     * @return CompilerPassInterface
     */
    public function createCompilerPass()
    {
        return new CompositeContributorsProviderCompilerPass($this->batchContributionTag, $this->batchContributionTag, $this);
    }

    /**
     * @param string $category
     *
     * @return ExtensionPoint
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param $contributionTag
     *
     * @return ExtensionPoint
     */
    public function setSingleContributionTag($contributionTag)
    {
        $this->singleContributionTag = $contributionTag;

        return $this;
    }

    /**
     * @return string
     */
    public function getSingleContributionTag()
    {
        if (!$this->singleContributionTag) {
            return $this->id;
        }

        return $this->singleContributionTag;
    }

    /**
     * @param string $description
     *
     * @return ExtensionPoint
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
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

    /**
     * @param string $detailedDescription
     *
     * @return ExtensionPoint
     */
    public function setDetailedDescription($detailedDescription)
    {
        $this->detailedDescription = $detailedDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getDetailedDescription()
    {
        return $this->detailedDescription;
    }

    /**
     * @return bool
     */
    public function isDetailedDescriptionAvailable()
    {
        return !!$this->detailedDescription;
    }

    static public function clazz()
    {
        return get_called_class();
    }
}