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

use Magento\Store\Model\ScopeInterface;

use Magento\Framework\UrlInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use DevBera\CmsLinkToMenu\Model\System\Config\Backend\CmsPageCustomLinker\Processor;
use DevBera\CmsLinkToMenu\Api\MenuLinkManagementInterface;

class MenuLinkManagement implements MenuLinkManagementInterface
{

    private $homePageIdentifier;
    
    /**
     * @var Processor
     */
    private $processor;

    const  XML_PATH_PRE_CMS_CUSTOM_LINKS = 'cmslinktomenu/cms_custom_links';
            
    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var NodeFactory
     */
    private $nodeFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        NodeFactory $nodeFactory,
        PageRepositoryInterface $pageRepository,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        ScopeConfigInterface  $scopeConfig,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        Processor  $processor,
        \Psr\Log\LoggerInterface $logger
    ) {
       
        $this->logger = $logger;
        $this->filterBuilder = $filterBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->urlBuilder = $urlBuilder;
        $this->nodeFactory = $nodeFactory;
        $this->pageRepository = $pageRepository;
        $this->processor = $processor;
    }
    
    public function addLinks($subject, $position = 'left')
    {
        $fieldValue =  $this->scopeConfig->getValue(
            self::XML_PATH_PRE_CMS_CUSTOM_LINKS.'/'.$position,
            ScopeInterface::SCOPE_STORE
        );
        
        $menuItems = $this->getLinksWithSortOrder($fieldValue);
        
        if (!empty($menuItems) && is_array($menuItems)) {
            $this->buildMenuItems($subject, $menuItems);
        }
    }
    
    private function getLinksWithSortOrder($fieldValue)
    {
        
        $result = $this->processor->getFieldValueInArrayType($fieldValue);
        
        if (is_array($result) && !empty($result)) {
            
            usort($result, function ($a, $b) {
                return $a['position'] <=> $b['position'];
            });
            
            return  $result;
        }
        return false;
    }
    
    private function buildMenuItems($subject, $menuItems)
    {
        
        $cmsPages = $this->getFindCmsPageList($menuItems);

        foreach ($menuItems as $menuItem) {

            if ($menuItem['link_type'] === 1 && (empty($cmsPages) ||
                      !array_key_exists($menuItem['page_id'], $cmsPages))
            ) {
                continue;
            }
            
            if ($menuItem['link_type'] === 1) {

                $subject->getMenu()->addChild(
                    $this->addCmsPageToMenu($subject, $menuItem, $cmsPages)
                );
            }
            
            if ($menuItem['link_type'] === 2) {
                $subject->getMenu()->addChild(
                    $this->addStaticLinkToMenu($subject, $menuItem)
                );
            }
        }
    }

    private function addCmsPageToMenu($subject, $menuItem, $cmsPages)
    {
            
        $page = $cmsPages[$menuItem['page_id']];

        $node = $this->nodeFactory->create(
            [
                'data' => [
                    'name' => $menuItem['link_text'],
                    'id' => 'cms-page-'.$menuItem['position'].'-'.$page->getIdentifier(),
                    'url' => $this->getCmsPageUrl($page),
                    'has_active' => false,
                    'is_active' => false
                ],
                'idField' => 'id',
                'tree' => $subject->getMenu()->getTree()
            ]
        );
        return $node;
    }
    
    private function addStaticLinkToMenu($subject, $menuItem)
    {
        $node = $this->nodeFactory->create(
            [
                'data' => [
                    'name' => $menuItem['link_text'],
                    'id' => 'static-links-'.$menuItem['position'],
                    'url' => $this->getLinkUrl($menuItem),
                    'has_active' => false,
                    'is_active' => false
                ],
                'idField' => 'id',
                'tree' => $subject->getMenu()->getTree()
            ]
        );
        return $node;
    }
    
    private function getFindCmsPageList($links)
    {
        
        $cmsPagesIdentifier = [];
        $result = [];
        
        foreach ($links as $link) {
            if ($link['link_type'] == 1) {
                $cmsPagesIdentifier[] = $link['page_id'];
            }
        }
        array_unique($cmsPagesIdentifier);
        
        $this->searchCriteriaBuilder->addFilters(
            [
                $this->filterBuilder
                    ->setField('identifier')
                    ->setConditionType('in')
                    ->setValue($cmsPagesIdentifier)
                    ->create(),
            ]
        );

        $this->searchCriteriaBuilder->addFilters(
            [
                $this->filterBuilder
                    ->setField('is_active')
                    ->setConditionType('eq')
                    ->setValue(true)
                    ->create(),
            ]
        );
        
        $this->searchCriteriaBuilder->setCurrentPage(1)->setPageSize(count($cmsPagesIdentifier));
        
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $pages = $this->pageRepository->getList($searchCriteria);

        if ($pages->getTotalCount() >0) {
            $items = $pages->getItems();
            foreach ($items as $page) {
                   $result[$page->getIdentifier()] =  $page;
            }
        }
        return $result;
    }

    private function getLinkUrl($data)
    {
        return $this->urlBuilder->getUrl(null, ['_direct' => $data['link_url']]);
    }
    /**
     *
     * @return string
     */
    private function getHomePageIdentifier()
    {
        if (!$this->homePageIdentifier) {
            
            $this->homePageIdentifier = $this->scopeConfig->getValue(
                'web/default/cms_home_page',
                ScopeInterface::SCOPE_STORE
            );
        }
        return $this->homePageIdentifier;
    }
    private function getCmsPageUrl($page)
    {
        if ($page->getIdentifier() == $this->getHomePageIdentifier()) {
            return $this->urlBuilder->getUrl();
        }
        return $this->urlBuilder->getUrl(null, ['_direct' => $page->getIdentifier()]);
    }
}
