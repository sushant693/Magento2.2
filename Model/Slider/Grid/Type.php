<?php
/**
 * Copyright © 2017 sushant Kumar (sushant693@gmail.com) All rights reserved.
 */

namespace SuMage\CatalogSlider\Model\Slider\Grid;

class Type implements \Magento\Framework\Data\OptionSourceInterface{

    /**
     * To option slider types array
     * @return array
     */
    public function toOptionArray(){
        return \SuMage\CatalogSlider\Model\Catalogslider::getSliderTypeArray();
    }
}