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

namespace DevBera\CmsLinkToMenu\Model\System\Config\Backend\CmsPageCustomLinker;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Math\Random;

class Processor
{

    /**
     * @var \Magento\Framework\Math\Random
     */
    private $mathRandom;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    public function __construct(
        Json $jsonSerializer,
        Random $mathRandom
    ) {
        $this->jsonSerializer = $jsonSerializer;
        $this->mathRandom = $mathRandom;
    }

    public function buildValueForSave($value)
    {
        $this->validateFieldValue($value);

        if ($this->validateFieldValue($value)) {
            $value = $this->serializeField($value);
        } else {
            $value = [];
        }

        return $this->jsonSerializer->serialize($value);
    }

    public function convertFieldToArrayType($value)
    {
        
        $value = $this->unSerializeField($value);
        $value = $this->loadAbleValue($value);
        return $value;
    }

    private function validateFieldValue($value)
    {
        if (!is_array($value)) {
            return false;
        }

        unset($value['__empty']);

        foreach ($value as $eachRow) {

            if (!is_array($eachRow) || !array_key_exists('link_type', $eachRow)
                    || !array_key_exists('link_text', $eachRow)
            ) {
                return false;
            }
        }
        return true;
    }

    private function serializeField($value)
    {
        
        $result = [];
        unset($value['__empty']);
  
        foreach ($value as $eachRow) {

            if (!is_array($eachRow) || !array_key_exists('link_type', $eachRow)
                    || !array_key_exists('link_text', $eachRow)
            ) {
                continue;
            }

            $linkType = (int) $eachRow['link_type'];
            /**
             * If link type is CMS Page
             */
            if (($linkType == 1 && !array_key_exists('page_id', $eachRow))
                    || ($linkType == 2 && !array_key_exists('link_url', $eachRow))
                    || ($linkType == 2 && array_key_exists('link_url', $eachRow) && empty($eachRow['link_url']))
                    ) {
                continue;
            }

            $position = (array_key_exists('position', $eachRow))
                    && (empty($eachRow['position']) === false) ? $eachRow['position'] : 0;

            $result[] = [
                'link_type' => $linkType,
                'link_text' => $eachRow['link_text'],
                'page_id' => ($linkType == 1) ? $eachRow['page_id'] : null,
                'link_url' => ($linkType == 2) ? $eachRow['link_url'] : null,
                'position' => $position
            ];
        }
        return $result;
    }

    private function unSerializeField($value)
    {
        if (is_string($value) && !empty($value)) {
            return $this->jsonSerializer->unserialize($value);
        }
        return [];
    }

    private function loadAbleValue($value)
    {
        
        $result = [];
        if ($this->validateFieldValue($value)) {
            foreach ($value as $eachRow) {
                
                $linkType = (int) $eachRow['link_type'];
                
                $uniqueHashId = $this->mathRandom->getUniqueHash('_');
                $result[$uniqueHashId] = [
                    'link_type' => $eachRow['link_type'],
                    'link_text' => $eachRow['link_text'],
                    'page_id' => ($linkType == 1) ? $eachRow['page_id'] : null,
                    'link_url' => ($linkType == 2) ? $eachRow['link_url'] : null,
                    'position' => $eachRow['position']
                ];
            }
        }

        return $result;
    }
    
    public function getFieldValueInArrayType($value)
    {
        
        $value = $this->unSerializeField($value);
        $value = $this->loadSystemValue($value);
        return $value;
    }
    private function loadSystemValue($value)
    {
        
        $result = [];
        if ($this->validateFieldValue($value)) {
            foreach ($value as $eachRow) {
                
                $linkType = (int) $eachRow['link_type'];
       
                $result[] = [
                    'link_type' => $eachRow['link_type'],
                    'link_text' => $eachRow['link_text'],
                    'page_id' => ($linkType == 1) ? $eachRow['page_id'] : null,
                    'link_url' => ($linkType == 2) ? $eachRow['link_url'] : null,
                    'position' => $eachRow['position']
                ];
            }
        }

        return $result;
    }
}
