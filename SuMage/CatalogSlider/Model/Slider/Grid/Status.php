<?php
/**
 * Copyright © 2017 sushant Kumar (sushant693@gmail.com) All rights reserved.
 */

namespace SuMage\CatalogSlider\Model\Slider\Grid;

class Status implements \Magento\Framework\Option\ArrayInterface {

    /**
     * To option slider statuses array
     * @return array
     */
    public function toOptionArray(){
        return \SuMage\CatalogSlider\Model\Catalogslider::getStatusArray();
    }
}