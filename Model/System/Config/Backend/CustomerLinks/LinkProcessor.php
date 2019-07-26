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

namespace DevBera\CmsLinkToMenu\Model\System\Config\Backend\CustomerLinks;

class LinkProcessor
{

    /**
     * @var \Magento\Framework\Math\Random
     */
    private $mathRandom;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json  $jsonSerializer,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {

        $this->scopeConfig = $scopeConfig;
        $this->jsonSerializer = $jsonSerializer;
        $this->mathRandom = $mathRandom;
    }
    
    public function buildValueForSave($value)
    {
        if ($this->isFieldValueValidate($value)) {
            $value = $this->makeJsonSerializeAbleValue($value);
        }
        $value = $this->jsonSerializer->serialize($value);
        return $value;
    }
    /**
     *  Make an array for during load of fields
     * @return type array
     */
    public function buildFieldToArrayType($value)
    {
        $value = $this->makeJsonUnSerializeAbleValue($value);
        $value = $this->makeEncodeArrayForLoad($value);
        return $value;
    }

    private function isFieldValueValidate($value)
    {
        if (!is_array($value)) {
            return false;
        }

        unset($value['__empty']);

        foreach ($value as $eachRow) {
            if (!is_array($eachRow) || !array_key_exists('link_text', $eachRow)
                    || !array_key_exists('link_url', $eachRow)
            ) {
                return false;
            }
        }
        return true;
    }

    private function makeJsonSerializeAbleValue($value)
    {

        $result = [];
        unset($value['__empty']);

        foreach ($value as $eachRow) {

            if (!is_array($eachRow) || !array_key_exists('link_text', $eachRow)
                    || !array_key_exists('link_url', $eachRow)
            ) {
                continue;
            }

            $position = (array_key_exists('position', $eachRow))
                    && (empty($eachRow['position']) === false) ? $eachRow['position'] : 0;

            $result[] = [
                'link_text' => $eachRow['link_text'],
                'link_url' => $eachRow['link_url'],
                'position' => $position
            ];
        }
        return $result;
    }

    private function makeJsonUnSerializeAbleValue($value)
    {
        if (is_string($value) && !empty($value)) {
            return $this->jsonSerializer->unserialize($value);
        }
        return [];
    }

    private function makeEncodeArrayForLoad($value)
    {
        $result = [];
        if ($this->isFieldValueValidate($value)) {
            foreach ($value as $eachRow) {
                $uniqueHashId = $this->mathRandom->getUniqueHash('_');
                $result[$uniqueHashId] = [
                    'link_text' => $eachRow['link_text'],
                    'link_url' => $eachRow['link_url'],
                    'position' => $eachRow['position']
                ];
            }
        }

        return $result;
    }
    public function getValueInArray($value)
    {
        $value = $this->makeJsonUnSerializeAbleValue($value);
        if (!$this->isFieldValueValidate($value)) {
            return [];
        }
        return $value;
    }
}
