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


namespace DevBera\CmsLinkToMenu\Block\Adminhtml\Form\Field;

class Pages extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * @var \DevBera\CmsLinkToMenu\Model\Config\Source\Pages
     */
    private $cmsPages;

    public function __construct(
        \DevBera\CmsLinkToMenu\Model\Config\Source\Pages $cmsPages,
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cmsPages = $cmsPages;
    }
    public function _toHtml()
    {
        // @codingStandardsIgnoreStart
        if (!$this->getOptions()) {
            foreach ($this->cmsPages->getOptions() as $pageIndetifier => $PageTitle) {
                $this->addOption($pageIndetifier, addslashes($PageTitle));
            }
        }
        // @codingStandardsIgnoreEnd
        return parent::_toHtml();
    }
    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
