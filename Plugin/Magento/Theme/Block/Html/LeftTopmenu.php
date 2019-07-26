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

namespace DevBera\CmsLinkToMenu\Plugin\Magento\Theme\Block\Html;

use Magento\Store\Model\ScopeInterface;

class LeftTopmenu
{

    /**
     * @var \DevBera\CmsLinkToMenu\Model\StaticLinkers
     */
    private $staticLinkers;

    const CONFIG_IS_ENABLED = 'cmslinktomenu/general/enable';
    
     const CONFIG_IS_CUSTOM_LINKS_ENABLED = 'cmslinktomenu/custom_links/enable';

    /**
     * @var \DevBera\CmsLinkToMenu\Model
     */
    private $cmsLinks;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    
    public function __construct(
        \DevBera\CmsLinkToMenu\Model\AddItemtoMenu $cmsLinks,
        \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig,
        \DevBera\CmsLinkToMenu\Model\StaticLinkers $staticLinkers,
        \Psr\Log\LoggerInterface $logger
    ) {
        
        $this->logger = $logger;
        $this->cmsLinks = $cmsLinks;
        $this->scopeConfig = $scopeConfig;
        $this->staticLinkers = $staticLinkers;
    }

    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $limit = 0,
        $childrenWrapClass = '',
        $outermostClass = ''
    ) {
        
        $isAddCmsPageToMenuEnabled = $this->isAddCmsPageToMenuEnabled();
        $isAddCustomLinkToMenuEnabled = $this->isAddCustomLinkToMenuEnabled();
        
        if ($isAddCmsPageToMenuEnabled || $isAddCustomLinkToMenuEnabled) {
            
            if ($isAddCmsPageToMenuEnabled) {
                $this->cmsLinks->addCmsPagesToMenu($subject);
            }

            if ($isAddCustomLinkToMenuEnabled) {
                $this->staticLinkers->addCustomLinks($subject);
            }
            
            return [$limit, $childrenWrapClass, $outermostClass];
        }
    }
    
    private function isAddCmsPageToMenuEnabled()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_IS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
    private function isAddCustomLinkToMenuEnabled()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_IS_CUSTOM_LINKS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
}
