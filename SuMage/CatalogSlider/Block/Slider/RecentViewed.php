<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Copyright © 2017 sushant Kumar (sushant693@gmail.com) All rights reserved.
 */

namespace SuMage\CatalogSlider\Block\Slider;

class RecentViewed extends \Magento\Reports\Block\Product\Viewed implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
//        $this->addColumnCountLayoutDepend('1column', 5)
//            ->addColumnCountLayoutDepend('2columns-left', 4)
//            ->addColumnCountLayoutDepend('2columns-right', 4)
//            ->addColumnCountLayoutDepend('3columns', 3);
    }
    
    /**
     * void
     */
    public function abc() {
        print_r('my block');
//        var_dump($this->getRecentlyViewedProductsBlock());
//        return $this->getRecentlyViewedProductsBlock();
    }
}
