<?php
namespace Twinsen\InkyEmails\Block;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class EmailBlock extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        Template\Context $context,
        Registry $registry,
        array $data = [])
    {
        $data = array_merge($data, $registry->registry('email_data'));
        parent::__construct($context, $data);
    }
}

