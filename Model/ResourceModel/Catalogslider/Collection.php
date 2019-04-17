<?php
/**
 * Copyright © 2017 sushant Kumar (sushant693@gmail.com) All rights reserved.
 */

namespace SuMage\CatalogSlider\Model\ResourceModel\Catalogslider;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    /**
     * Initialize resources
     * @return void
     */
    protected function _construct(){
        $this->_init('SuMage\CatalogSlider\Model\Catalogslider','SuMage\CatalogSlider\Model\ResourceModel\Catalogslider');
    }

}