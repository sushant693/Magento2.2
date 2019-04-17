<?php
/**
 * Copyright © 2017 sushant Kumar (sushant693@gmail.com) All rights reserved.
 */

namespace SuMage\CatalogSlider\Controller\Adminhtml\Slider;

class Index extends \SuMage\CatalogSlider\Controller\Adminhtml\Slider {

    /**
     * Slider index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(){
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->_resultForwardFactory->create();
            $resultForward->forward('grid');
            return $resultForward;
        }
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('SuMage_Core::admin');

        $resultPage->addBreadcrumb(__('Sliders'), __('Sliders'));
        $resultPage->addBreadcrumb(__('Manage Sliders'), __('Manage Sliders'));

        return $resultPage;
    }
}