<?php
namespace Twinsen\InkyEmails\Model;

use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Mail\TemplateInterface;
use Hampe\Inky\Inky;
use Magento\Framework\View\Asset\GroupedCollection;
use Pelago\Emogrifier;
use Magento\Framework\Registry;

class InkyTemplate implements \Magento\Framework\Mail\TemplateInterface
{

    /**
     * @var string Name of the Layout Handle to Render
     */
    protected $layoutHandle;

    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layoutInterface;
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    private $pageConfig;


    public function __construct(
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\View\LayoutInterfaceFactory $layoutInterfaceFactory,
        Registry $registry,
        $data = array())
    {
        $this->layoutHandle = $data['template_id'];
        $this->layoutInterface = $layoutInterfaceFactory->create();

        $this->registry = $registry;

        $this->pageConfig = $pageConfig;
    }

    public function renderLayout($layoutHandleId)
    {

        $layout = $this->layoutInterface;
        $update = $layout->getUpdate();
        $update->addHandle($layoutHandleId);

        $update->load();
        $layout->generateXml();
        $layout->generateElements();
        $layout->addOutputElement('root');
        $output = $layout->getOutput();


        return $output;

    }

    public function getCss()
    {
        $fullCss = "";
        $assetCollection = $this->pageConfig->getAssetCollection();
        foreach ($assetCollection->getGroups() as $group) {
            $properties = $group->getProperties();
            if ($properties[GroupedCollection::PROPERTY_CONTENT_TYPE] == "css") {
                $cssFiles = $group->getAll();
                foreach ($cssFiles as $cssFile) {
                    /** @var \Magento\Framework\View\Asset\File $cssFile */
                    $fullCss .= $cssFile->getContent();
                }
            }
        }

        return $fullCss;
    }

    public function processTemplate()
    {

        $inkyHtml = $this->renderLayout($this->layoutHandle);
        $html = $this->processInky($inkyHtml);
        $css = $this->getCss();

        $emogrifier = new Emogrifier($html, $css);
        $retHtml = $emogrifier->emogrify();
        $retHtml = (string)$retHtml;
        return $retHtml;
    }

    public function processInky($inkyHtml)
    {
        $gridColumns = 12; //optional, default is 12
        $additionalComponentFactories = []; //optional
        $inky = new Inky($gridColumns, $additionalComponentFactories);
        return $inky->releaseTheKraken($inkyHtml);
    }

    public function getSubject()
    {
        return "";
    }

    public function setVars(array $vars)
    {
        $this->registry->register('email_data', $vars);
        return $this;
    }

    public function setOptions(array $options)
    {
        return $this;
    }

    public function isPlain()
    {
        return false;
    }

    public function getType()
    {
        return TemplateTypesInterface::TYPE_HTML;
    }
}