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

class LinkTypes extends \Magento\Framework\View\Element\Html\Select
{

    /**
     * @var \DevBera\CmsLinkToMenu\Model\Config\Source\LinkTypes
     */
    private $configLinkTypes;

    public function __construct(
        \DevBera\CmsLinkToMenu\Model\Config\Source\LinkTypes $configLinkTypes,
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configLinkTypes = $configLinkTypes;
    }
    public function _toHtml()
    {
        // @codingStandardsIgnoreStart
        if (!$this->getOptions()) {
            foreach ($this->configLinkTypes->getOptions() as $linkValue => $linkType) {
                $this->addOption($linkValue, addslashes($linkType));
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
