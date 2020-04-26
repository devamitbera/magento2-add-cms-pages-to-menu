<?php
namespace DevBera\CmsLinkToMenu\Block\Html\Topmenu;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use DevBera\CmsLinkToMenu\Api\MenuLinkManagementInterface;

class Js extends Template
{
    /**
     * @var MenuLinkManagementInterface
     */
    private $menuLinkManagement;
    /**
     * @var Json
     */
    private $jsonEncoder;

    public function __construct(
        Context $context,
        MenuLinkManagementInterface $menuLinkManagement,
        Json $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->menuLinkManagement = $menuLinkManagement;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     *  Get List of External Link list
     *
     * @return bool|false|string
     */
    public function getJsonLinks()
    {
        return $this->jsonEncoder->serialize($this->menuLinkManagement->getTargetBlanksLinks());
    }

    /**
     *  Target Blank  flag
     * @return int
     */
    public function isOpenInNewTab()
    {
        return $this->menuLinkManagement->isOpenInTab();
    }
}
