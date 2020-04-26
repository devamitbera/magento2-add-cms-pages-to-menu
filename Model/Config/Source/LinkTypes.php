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

namespace DevBera\CmsLinkToMenu\Model\Config\Source;

class LinkTypes implements \Magento\Framework\Option\ArrayInterface
{

    private $options;

    public function toOptionArray(): array
    {
        if (!$this->options) {
            $this->options[] = ['value' => 1,'label'=> __('Cms Page')];
            $this->options[] = ['value' => 2,'label'=> __('Custom Link /Static Link')];
            $this->options[] = ['value' => 3,'label'=> __('3rd party Link')];
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
