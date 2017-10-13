<?php
/**
 * Copyright © 2017 sushant Kumar (sushant693@gmail.com) All rights reserved.
 */

namespace SuMage\CatalogSlider\Controller\Adminhtml\Slider;

class Edit extends \SuMage\CatalogSlider\Controller\Adminhtml\Slider {

    /**
     * Edit slider page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(){

        $sliderId = (int)$this->getRequest()->getParam('id', false);

        $model = $this->_initSlider($sliderId);
        if ($sliderId)
        {
            if (!$model->getId()) {
                $this->messageManager->addError(__('This slider no longer exists.'));
                $resultForward = $this->_resultRedirectFactory->create();
                return $resultForward->setPath('*/*/');
            }
        }


        /**
         * Set entered data if there was an error when saving
         */
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('product_slider', $model);

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Product Slider'));
        return $resultPage;
    }

}