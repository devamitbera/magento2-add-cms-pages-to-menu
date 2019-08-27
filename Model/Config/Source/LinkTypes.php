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

namespace DevBera\CmsLinkToMenu\Model\Config\Source;

class LinkTypes implements \Magento\Framework\Option\ArrayInterface
{

    private $options;
    
    public function toOptionArray(): array
    {
        if (!$this->options) {
            $this->options[] = ['value' => 1,'label'=> __('Cms Page')];
            $this->options[] = ['value' => 2,'label'=> __('Custom Link /Static Link')];
        }
        return $this->options;
    }
    
    public function getOptions()
    {
        $result = [];
        $options = $this->toOptionArray();

        foreach ($options as $option) {
            $result[$option['value']] = $option['label'];
        }
        return $result;
    }
}
