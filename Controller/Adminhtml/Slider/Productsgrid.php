<?php
/**
 * Copyright © 2017 sushant Kumar (sushant693@gmail.com) All rights reserved.
 */

namespace SuMage\CatalogSlider\Controller\Adminhtml\Slider;

class Productsgrid extends \SuMage\CatalogSlider\Controller\Adminhtml\Slider
{
    /**
     * Display list of additional products to current slider type
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $sliderId = (int)$this->getRequest()->getParam('id', false);

        $slider = $this->_initSlider($sliderId);
        $this->_coreRegistry->register('product_slider', $slider);

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->_resultRawFactory->create();
        return $resultRaw->setContents(
            $this->_layoutFactory->create()->createBlock(
                'SuMage\CatalogSlider\Block\Adminhtml\Slider\Edit\Tab\Products',
                'admin.block.slider.tab.products'
            )->toHtml()
        );
    }
}