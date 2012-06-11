<?php

namespace Sli\ExpanderBundle\Ext;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class Extension
{
    private $contributorServiceTagName;
    private $providerServiceId;

    public function __construct($providerServiceId, $contributorServiceTagName)
    {
        $this->providerServiceId = $providerServiceId;
        $this->contributorServiceTagName = $contributorServiceTagName;
    }

    public function getContributorServiceTagName()
    {
        return $this->contributorServiceTagName;
    }

    public function getProviderServiceId()
    {
        return $this->providerServiceId;
    }

    public function createCompilerPass()
    {
        return new CompilerPass($this);
    }

    /**
     * @return string
     */
    static public function clazz()
    {
        return get_called_class();
    }
}
