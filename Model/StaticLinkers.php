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
namespace DevBera\CmsLinkToMenu\Model;

use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Store\Model\ScopeInterface;
use DevBera\CmsLinkToMenu\Model\System\Config\Backend\CustomerLinks\LinkProcessor;

class StaticLinkers
{

    const  XML_PATH_CMSLINKTOMENU_CUSTOM_LINKS = 'cmslinktomenu/custom_links';
    
    /**
     * @var \DevBera\CmsLinkToMenu\Model\System\Config\Backend\CustomerLinks\LinkProcessor
     */
    private $customLinkProcessor;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var NodeFactory
     */
    private $nodeFactory;

    public function __construct(
        NodeFactory $nodeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig,
        LinkProcessor $customLinkProcessor,
        \Psr\Log\LoggerInterface $logger
    ) {
            
        $this->nodeFactory = $nodeFactory;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->customLinkProcessor = $customLinkProcessor;
    }
    
    public function addCustomLinks($subject, $position = 'left')
    {
        $linksList = $this->getCustomLinksWithSortOrder($position);

        if ($linksList && !empty($linksList)) {
            $i = 0;
            foreach ($linksList as $link) {
                $subject->getMenu()->addChild(
                    $this->buildMenuItem($link, $subject, $position, $i++)
                );
            }
        }
    }
    
    private function getCustomLinksWithSortOrder($position = 'left')
    {
        $fieldValue =  $this->scopeConfig->getValue(
            self::XML_PATH_CMSLINKTOMENU_CUSTOM_LINKS.'/'.$position,
            ScopeInterface::SCOPE_STORE
        );
        $fieldValue = $this->customLinkProcessor->getValueInArray($fieldValue);

        if (is_array($fieldValue) && !empty($fieldValue)) {
            $menuItems =  $this->buildArray($fieldValue);
            return  $menuItems;
        }
        return false;
    }
    
    private function buildArray($value)
    {
        $result = [];
        foreach ($value as $eachRow) {
            if (!is_array($eachRow) || !array_key_exists('link_text', $eachRow)
                    || !array_key_exists('link_url', $eachRow)
                    || !array_key_exists('position', $eachRow)
            ) {
                continue;
            }

            $result[] = [
                'link_text' => $eachRow['link_text'],
                'link_url' => $eachRow['link_url'],
                'position' => $eachRow['position']
            ];
        }
        
        if (!empty($result)) {
            usort($result, function ($a, $b) {
                return $a['position'] <=> $b['position'];
            });
        }
        
        return $result;
    }
    
    private function buildMenuItem($data, $subject, $position, $itemPosition)
    {
        $node = $this->nodeFactory->create(
            [
                'data' => [
                    'name' => $this->getPageTitle($data['link_text']),
                    'id' => 'static-links-'.$position.'-'.$itemPosition,
                    'url' => $this->getLinkUrl($data),
                    'has_active' => false,
                    'is_active' => false
                ],
                'idField' => 'id',
                'tree' => $subject->getMenu()->getTree()
            ]
        );
        return $node;
    }
    
    private function getPageTitle($title)
    {
        return __($title);
    }
    private function getLinkUrl($data)
    {
        return $this->urlBuilder->getUrl(null, ['_direct' => $data['link_url']]);
    }
}
