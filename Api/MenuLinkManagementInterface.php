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


namespace DevBera\CmsLinkToMenu\Api;

interface MenuLinkManagementInterface
{
    /**
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param string $position
     * @return void
     */
    public function addLinks($subject, $position = 'left');
}
