<?php
/**
 * Copyright © 2017 sushant Kumar (sushant693@gmail.com) All rights reserved.
 */

namespace SuMage\CatalogSlider\Model\Slider\Grid;

/**
 * To option slider locations array
 * @return array
 */
class Location implements \Magento\Framework\Option\ArrayInterface{

    public function toOptionArray(){
        return \SuMage\CatalogSlider\Model\Catalogslider::getSliderGridLocations();
    }
}