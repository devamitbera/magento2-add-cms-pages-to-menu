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


class AddItemtoMenu 
{

    private $homePageIdentifier;
    
    /**
     * @var \DevBera\CmsLinkToMenu\Model\System\Config\Backend\FieldProcessor
     */
    private $fieldProcessor;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    const XM_PATH_CMSLINKTOMENU_GENERAL_PAGES = 'cmslinktomenu/general/pages';

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

    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    
    public function __construct(
       NodeFactory $nodeFactory,
       \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
       \Magento\Store\Model\StoreManagerInterface $storeManager,
       \Magento\Framework\UrlInterface $urlBuilder,
       \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig,
       \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
       \Magento\Framework\Api\FilterBuilder $filterBuilder,
       \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
       \DevBera\CmsLinkToMenu\Model\System\Config\Backend\FieldProcessor $fieldProcessor,   
       \Psr\Log\LoggerInterface $logger
    ) {
        
        $this->pageRepository = $pageRepository;
        $this->nodeFactory = $nodeFactory;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->fieldProcessor = $fieldProcessor;
    }
    
    /**
     * Get List of Cms Pages from settings
     * 
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     */
        public function addCmsPagesToMenu($subject,$position = 'left')
    {
        $pagesIdentifierWithFieldPosition = [];
        
        
       $fieldName = $position.'_cms_pages';
       
       $pagesIdentifierWithFieldPosition = $this->fieldProcessor->getConfigValue($fieldName, $this->storeManager->getStore());
       
        //  Sort $pagesIdentifierWithFieldPosition arrays in ascending order, according to the value 
       if(!empty($pagesIdentifierWithFieldPosition)){
           asort($pagesIdentifierWithFieldPosition) ;
       }
       
       $menuItems = $this->getPagesListWithSortOrder($pagesIdentifierWithFieldPosition);
       
       $homePageIdentifier = $this->getHomePageIdentifier(); 
       
        if(!empty($menuItems)){
            foreach($menuItems as $page){
                $subject->getMenu()->addChild(
                        $this->buildMenuItem($page, $subject,$position)
                );                
            }
            
        }
    }
    
    private function buildMenuItem($page,$subject,$position)
    {
        $node = $this->nodeFactory->create(
            [
                'data' => [
                    'name' => $this->getCmsPageTitle($page),
                    'id' => 'cms-page-'.$position.'-'.$page->getIdentifier(),
                    'url' => $this->getCmsPageUrl($page),
                    'has_active' => false,
                    'is_active' => false // (expression to determine if menu item is selected or not)
                ],
                'idField' => 'id',
                'tree' => $subject->getMenu()->getTree()
            ]
        );
        return $node;
    }
    
    /**
     * 
     * @param array $pagesIdentifierWithFieldPosition
     * @return array
     */
    private function avaliableCmsPage($pagesIdentifierWithFieldPosition)
    {
        return array_keys($pagesIdentifierWithFieldPosition);
    }
    
    /**
     * 
     * @param array $pagesIdentifierWithFieldPosition
     * @return array
     */
    private function getPagesListWithSortOrder($pagesIdentifierWithFieldPosition)
    {
        $result = [];
        $menuItems = [];
        
        $cmsPagesIdentifier = $this->avaliableCmsPage($pagesIdentifierWithFieldPosition);
        
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

        if($pages->getTotalCount() >0 ){
            $items = $pages->getItems();
            foreach($items as $page){
                   $result[$page->getIdentifier()] =  $page;         
            }            
        }
        
        foreach($pagesIdentifierWithFieldPosition as $cmsIdentifier=>$postion){
            if(array_key_exists($cmsIdentifier, $result)){
                $menuItems[] = $result[$cmsIdentifier];
            }
        }
        
        return $menuItems;
    }
    
    private function getCmsPageUrl($page)
    {
        if($page->getIdentifier() == $this->getHomePageIdentifier()){
            return $this->urlBuilder->getUrl();
        }
        return $this->urlBuilder->getUrl(null, ['_direct' => $page->getIdentifier()]); 
    }

    /**
     * 
     * @return string
     */
    private function getHomePageIdentifier()
    {
        if(!$this->homePageIdentifier){
            
            $this->homePageIdentifier = $this->scopeConfig->getValue(
                'web/default/cms_home_page',
                ScopeInterface::SCOPE_STORE
            );
        }
      return $this->homePageIdentifier;
    }
    private function getCmsPageTitle($page)
    {
        if($page->getIdentifier() == $this->getHomePageIdentifier()){
            return __('Home');
        }
        return __($page->getTitle()); 
    } 
}
