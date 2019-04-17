<?php
/**
 * Copyright © 2017 sushant Kumar (sushant693@gmail.com) All rights reserved.
 */

namespace SuMage\CatalogSlider\Controller\Adminhtml\Slider;

class NewAction extends \SuMage\CatalogSlider\Controller\Adminhtml\Slider
{
    /**
     * Create new slider action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute(){
        //Forward to the edit action
        $resultForward = $this->_resultForwardFactory->create();
        $resultForward->forward('edit');
        return $resultForward;
    }
}