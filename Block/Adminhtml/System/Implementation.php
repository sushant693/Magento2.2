<?php
/**
 * Copyright © 2017 sushant Kumar (sushant693@gmail.com) All rights reserved.
 */

namespace SuMage\CatalogSlider\Block\Adminhtml\System;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Implementation extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = 'hello';

        return $html;

    }
}