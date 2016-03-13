<?php

namespace Sli\ExpanderBundle\Generation;

use Doctrine\Common\Util\Inflector;
use Sli\ExpanderBundle\Ext\ExtensionPoint;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class StandardContributionGenerator implements ContributionGeneratorInterface
{
    private $config = array();

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(BundleInterface $bundle, ExtensionPoint $ep, InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        if (!file_exists($bundle->getPath().'/Contributions')) {
            $output->writeln('Creating contributions directory ...');

            mkdir($bundle->getPath().'/Contributions');
        }

        while (!isset($this->config['className']) || !$this->isValidClassName($this->config['className'])) {
            /* @var DialogHelper $dialog */
            $dialog = $helperSet->get('dialog');

            $this->config['className'] = $dialog->ask($output, '<info>Please specify a contribution class name:</info> ');
        }

        $contributionFilename = $bundle->getPath().'/Contributions/'.$this->config['className'].'.php';
        if (file_exists($contributionFilename)) {
            throw new \RuntimeException("File '$contributionFilename' already exists!");
        }

        $servicesFilename = $bundle->getPath().'/Resources/config/services.xml';
        if (!file_exists($servicesFilename)) {
            throw new \RuntimeException("File '$servicesFilename' doesn't exist.");
        }

        $servicesXml = file_get_contents($servicesFilename);

        file_put_contents($contributionFilename, $this->compileContributionClassTemplate($bundle, $ep));
        file_put_contents($servicesFilename, $this->compileServicesXml($servicesXml, $bundle, $ep));

        $output->writeln('Done!');
        $output->writeln(' - New file: '.$contributionFilename);
        $output->writeln(' - Updated: '.$servicesFilename);
    }

    /**
     * @private
     *
     * @param string $className
     *
     * @return bool
     */
    public function isValidClassName($className)
    {
        // a simple validation against accidental mistyping rather than
        // a fully-fledged class name validation
        return '' !== $className && false === strpos($className, ' ');
    }

    /**
     * @return string
     */
    protected function getServiceXmlTemplate()
    {
        return <<<TPL

        <service id="%id%"
                 class="%class_name%">

            <tag name="%tag_name%" />
        </service>
TPL;
    }

    /**
     * @throws \RuntimeException
     *
     * @param string          $servicesFilename
     * @param BundleInterface $bundle
     * @param ExtensionPoint  $ep
     *
     * @return string
     */
    protected function compileServicesXml($servicesXml, BundleInterface $bundle, ExtensionPoint $ep)
    {
        $tpl = $this->getServiceXmlTemplate();

        $bundleServicesNamespace = substr(Inflector::tableize($bundle->getName()), 0, -1 * strlen('_bundle'));
        $serviceId = $bundleServicesNamespace.'.contributions.'.Inflector::tableize($this->config['className']);

        $className = $bundle->getNamespace().'\\Contributions\\'.$this->config['className'];

        $tagName = $ep->getBatchContributionTag();

        $compiledServiceXml = str_replace(array('%id%', '%class_name%', '%tag_name%'), array($serviceId, $className, $tagName), $tpl);
        $compiledServiceXmlAsArray = explode("\n", $compiledServiceXml);

        $servicesXmlAsArray = explode("\n", $servicesXml);

        $closingServicesTagIndex = null;
        foreach ($servicesXmlAsArray as $lineIndex => $rootLine) {
            // we are going to add a new service right before a closing </services> tag
            if (trim($rootLine) == '</services>') {
                $closingServicesTagIndex = $lineIndex;
            }
        }

        if (null === $closingServicesTagIndex) {
            throw new \RuntimeException('Unable to find a closing </services> tag!');
        }

        $resultXmlArray = array();
        foreach ($servicesXmlAsArray as $lineIndex => $rootLine) {
            if ($lineIndex == $closingServicesTagIndex) {
                foreach ($compiledServiceXmlAsArray as $innerLine) {
                    $resultXmlArray[] = $innerLine;
                }
            }

            $resultXmlArray[] = $rootLine;
        }

        return implode("\n", $resultXmlArray);
    }

    /**
     * @return string
     */
    protected function getContributionClassTemplate()
    {
        return <<<TPL
<?php

namespace %namespace%\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

class %class_name% implements ContributorInterface
{
    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return array(
        );
    }
}
TPL;
    }

    /**
     * @param BundleInterface $bundle
     * @param ExtensionPoint  $ep
     *
     * @return string
     */
    protected function compileContributionClassTemplate(BundleInterface $bundle, ExtensionPoint $ep)
    {
        $tpl = $this->getContributionClassTemplate();

        return str_replace(
            array('%namespace%', '%class_name%'), array($bundle->getNamespace(), $this->config['className']), $tpl
        );
    }
}
