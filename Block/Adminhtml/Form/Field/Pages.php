<?php

/**
 * A Magento 2 module named DevBera/CmsLinkToMenu
 * Copyright (C) 2019 Copyright 2019 © amitbera.com. All Rights Reserved
 *
 * This file included in DevBera/CmsLinkToMenu is licensed under OSL 3.0
 *
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */


namespace DevBera\CmsLinkToMenu\Block\Adminhtml\Form\Field;

class Pages extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * @var \DevBera\CmsLinkToMenu\Model\Config\Source\Pages
     */
    private $cmsPages;

    public function __construct(
        \DevBera\CmsLinkToMenu\Model\Config\Source\Pages $cmsPages,
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cmsPages = $cmsPages;
    }
    public function _toHtml()
    {
        // @codingStandardsIgnoreStart
        if (!$this->getOptions()) {
            foreach ($this->cmsPages->getOptions() as $pageIndetifier => $PageTitle) {
                $this->addOption($pageIndetifier, addslashes($PageTitle));
            }
        }
        // @codingStandardsIgnoreEnd
        return parent::_toHtml();
    }
    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
