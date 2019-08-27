<?php

/**
 * A Magento 2 module named DevBera/CmsLinkToMenu
 * Copyright (C) 2019 Copyright amitbera.com. All Rights Reserved
 *
 * This file included in DevBera/CmsLinkToMenu is licensed under OSL 3.0
 *
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace DevBera\CmsLinkToMenu\Block\Adminhtml\Form\Field;

class LinkTypes extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * @var \DevBera\CmsLinkToMenu\Model\Config\Source\LinkTypes
     */
    private $configLinkTypes;

    public function __construct(
        \DevBera\CmsLinkToMenu\Model\Config\Source\LinkTypes $configLinkTypes,
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configLinkTypes = $configLinkTypes;
    }
    public function _toHtml()
    {
        // @codingStandardsIgnoreStart
        if (!$this->getOptions()) {
            foreach ($this->configLinkTypes->getOptions() as $linkValue => $linkType) {
                $this->addOption($linkValue, addslashes($linkType));
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
