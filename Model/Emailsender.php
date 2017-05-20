<?php
namespace Twinsen\InkyEmails\Model;
class Emailsender
{
    public function __construct(
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper
    )
    {
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
    }

    public function sendEmail($fromEmail,$toEmail, $vars)
    {


        $this->inlineTranslation->suspend();

        $sender = [
            'name' => "New Mail",
            'email' => $fromEmail,
        ];


        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $transport = $this->_transportBuilder
            // Pass the Layout Handle here:
            ->setTemplateIdentifier('twinsen_inkymails_email')// this code we have mentioned in the email_templates.xml
            ->setTemplateModel('Twinsen\InkyEmails\Model\InkyTemplate')
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars($vars)
            ->setFrom($sender)
            ->addTo($toEmail)
            ->getTransport();

        $transport->sendMessage();;
        $this->inlineTranslation->resume();
    }
}