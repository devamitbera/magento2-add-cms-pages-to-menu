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

namespace DevBera\CmsLinkToMenu\Api;

interface MenuLinkManagementInterface
{
    /**
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param string $position
     * @return void
     */
    public function addLinks($subject, $position = 'left');

    /**
     * @return string []
     */
    public function getTargetBlanksLinks();
    /**
     *  get open tab
     * @return int
     */
    public function isOpenInTab();
}
