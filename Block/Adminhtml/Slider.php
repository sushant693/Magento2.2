<?php
/**
 * Copyright © 2017 sushant Kumar (sushant693@gmail.com) All rights reserved.
 */

namespace SuMage\CatalogSlider\Block\Adminhtml;

class Slider extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Modify header & button labels
     *
     * @return void
     */
    protected function _construct(){
        $this->_blockGroup = 'SuMage_CatalogSlider';
        $this->_controller = 'adminhtml';
        $this->_headerText = 'Slider';
        $this->_addButtonLabel = __('Create New Slider');
        parent::_construct();
    }

}