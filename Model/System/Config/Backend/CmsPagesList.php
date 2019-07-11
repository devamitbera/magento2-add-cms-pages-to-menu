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

namespace DevBera\CmsLinkToMenu\Model\System\Config\Backend;


class CmsPagesList  extends \Magento\Framework\App\Config\Value
{

    /**
     * @var \DevBera\CmsLinkToMenu\Model\System\Config\Backend\FieldProcessor
     */
    private $fieldProcessor;

    public function __construct(
           \Magento\Framework\Model\Context $context,
           \Magento\Framework\Registry $registry,
           \Magento\Framework\App\Config\ScopeConfigInterface $config,
           \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
           \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
           \DevBera\CmsLinkToMenu\Model\System\Config\Backend\FieldProcessor $fieldProcessor,
           \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
           array $data = array())
     {
        $this->fieldProcessor = $fieldProcessor;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }
    
    protected function _afterLoad() {
        $value = $this->getValue();
        $value = $this->fieldProcessor->makeArrayFieldValue($value);
        $this->setValue($value);
    }
    public function beforeSave()
    {
        $value = $this->getValue();
        $value = $this->fieldProcessor->buildStorableArrayFieldValue($value);
        $this->setValue($value);
    }   
}
