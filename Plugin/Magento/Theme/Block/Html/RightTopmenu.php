<?php
/**
 * DevBera
 *
 * @category   DevBera
 * @package    DevBera_CmsLinkToMenu
 * @author  Amit Bera (dev.amitbera@gmail.com)
 * @copyright  Copyright (c) 2020 Amit Bera (https://www.amitbera.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DevBera\CmsLinkToMenu\Plugin\Magento\Theme\Block\Html;

use Magento\Store\Model\ScopeInterface;

class RightTopmenu
{

    const XMLPATH_CONFIG_IS_ENABLED = 'cmslinktomenu/cms_custom_links/enable';

    /**
     * @var \DevBera\CmsLinkToMenu\Api\MenuLinkManagementInterface
     */
    private $menuLinkManagement;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig,
        \DevBera\CmsLinkToMenu\Api\MenuLinkManagementInterface $menuLinkManagement,
        \Psr\Log\LoggerInterface $logger
    ) {

        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->menuLinkManagement = $menuLinkManagement;
    }

    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $limit = 0,
        $childrenWrapClass = '',
        $outermostClass = ''
    ) {

        if ($this->isEnabled()) {
            $this->menuLinkManagement->addLinks($subject, 'right');
            return [$limit, $childrenWrapClass, $outermostClass];
        }
    }

    private function isEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XMLPATH_CONFIG_IS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
    public function afterToHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $html
    ) {
        $jsBlockOutPut =$subject->getLayout()
            ->createBlock(
                \DevBera\CmsLinkToMenu\Block\Html\Topmenu\Js::class,
                'cms.link.to.menu.js'
            )
            ->setTemplate('DevBera_CmsLinkToMenu::html/topmenu/js.phtml')
            ->toHtml();
        return $html.$jsBlockOutPut;
    }
}
