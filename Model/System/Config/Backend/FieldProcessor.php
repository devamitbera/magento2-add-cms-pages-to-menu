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

use Magento\Store\Model\ScopeInterface;

class FieldProcessor
{

    const XML_PATH_CMSLINKTOMENU_GENERAL_LEFT_PREFIX = 'cmslinktomenu/general/';
     
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Math\Random
     */
    private $mathRandom;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json  $jsonSerializer,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        
        $this->jsonSerializer = $jsonSerializer;
        $this->mathRandom = $mathRandom;
        $this->scopeConfig = $scopeConfig;
    }
    
    private function makeEncodeArrayField(array $value)
    {
        $options = [];
        foreach ($value as $pageIndetifier => $position) {
            $uniqueHashId = $this->mathRandom->getUniqueHash('_');
            $options[$uniqueHashId] = ['page_id' => $pageIndetifier, 'position' => $position?$position:0];
        }
        return $options;
    }
    
    /**
     * Make field to array from Json
     *
     * @param string $value
     * @return array
     */
    private function unserializeFieldValue($value)
    {
        if (is_string($value) && !empty($value)) {
            return $this->jsonSerializer->unserialize($value);
        } else {
            return [];
        }
    }
    
    /**
     * @param string|array $value
     * @return bool
     */
    private function isArrayFieldHasEncodedValue($value)
    {
        if (!is_array($value)) {
            return false;
        }
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists('page_id', $row)
                || !array_key_exists('position', $row)
            ) {
                return false;
            }
        }
        return true;
    }

    private function decodeArrayValue(array $value)
    {
        $result = [];
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
               || !array_key_exists('page_id', $row)
               || !array_key_exists('position', $row)
            ) {
                continue;
            }
             
            $cmsPageIndifier = $row['page_id'];
            $qty = !empty($row['position']) ? (float) $row['position'] : 0;
            $result[$cmsPageIndifier] = $qty;
        }
        return $result;
    }
    
    public function makeArrayFieldValue($value)
    {
        $value = $this->unserializeFieldValue($value);

        if (!$this->isArrayFieldHasEncodedValue($value)) {
            $value = $this->makeEncodeArrayField($value);
        }
        return $value;
    }
    public function buildStorableArrayFieldValue($value)
    {
        if ($this->isArrayFieldHasEncodedValue($value)) {
             $value = $this->decodeArrayFieldValue($value);
        }
        $value = $this->serializeValue($value);
        return $value;
    }
    private function decodeArrayFieldValue(array $value)
    {
        $result = [];
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists('page_id', $row)
                || !array_key_exists('position', $row)
            ) {
                continue;
            }
            $groupId = $row['page_id'];
            $qty = $row['position'];
            $result[$groupId] = $qty;
        }
        return $result;
    }

    private function serializeValue($value)
    {
        if (is_array($value)) {
            $data = [];
            foreach ($value as $pageIndetifier => $position) {
                if (!array_key_exists($pageIndetifier, $data)) {
                    $data[$pageIndetifier] = $this->getDefaultPostition($position);
                }
            }
            return $this->jsonSerializer->serialize($data);
        } else {
            return '';
        }
    }
    
    private function getDefaultPostition($position)
    {
        return !empty($position) ? (float) $position : 0;
    }
    
    /**
     * Get Field value as array
     *
     * @param string $fieldName
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     */
    public function getConfigValue($fieldName = 'left_cms_pages', $store = null)
    {
        $result = [];

        $value = $this->scopeConfig->getValue(
            self::XML_PATH_CMSLINKTOMENU_GENERAL_LEFT_PREFIX.$fieldName,
            ScopeInterface::SCOPE_STORE,
            $store
        );
     
        $result =$this->unserializeFieldValue($value);
        return $result;
    }
}
