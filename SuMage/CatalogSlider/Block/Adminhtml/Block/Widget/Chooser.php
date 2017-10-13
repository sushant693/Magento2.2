<?php
/**
 * Copyright © 2017 sushant Kumar (sushant693@gmail.com) All rights reserved.
 */

namespace SuMage\CatalogSlider\Block\Adminhtml\Block\Widget;

class Chooser extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \SuMage\CatalogSlider\Model\CatalogsliderFactory
     */
    protected $_catalogsliderFactory;

    /**
     * @var \SuMage\CatalogSlider\Model\ResourceModel\Catalogslider\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \SuMage\CatalogSlider\Model\CatalogsliderFactory $catalogsliderFactory
     * @param \SuMage\CatalogSlider\Model\ResourceModel\Catalogslider\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \SuMage\CatalogSlider\Model\CatalogsliderFactory $catalogsliderFactory,
        \SuMage\CatalogSlider\Model\ResourceModel\Catalogslider\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_catalogsliderFactory = $catalogsliderFactory;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('chooser_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setDefaultFilter(['chooser_is_active' => '1']);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl('catalogslider/widget/chooser', ['uniq_id' => $uniqId]);

        $chooser = $this->getLayout()->createBlock(
            'Magento\Widget\Block\Adminhtml\Widget\Chooser'
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        );

        if ($element->getValue()) {
            $slider = $this->_catalogsliderFactory->create()->load($element->getValue());
            if ($slider->getId()) {
                $chooser->setLabel($this->escapeHtml($slider->getTitle()));
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());

        return $element;
    }

    /**
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var sliderId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                var sliderTitle = trElement.down("td").next().innerHTML;
                ' .
            $chooserJsObject .
            '.setElementValue(sliderId);
                ' .
            $chooserJsObject .
            '.setElementLabel(sliderTitle);
                ' .
            $chooserJsObject .
            '.close();
            }
        ';
        return $js;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->_collectionFactory->create());
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'chooser_id',
            [
                'header' => __('ID'),
                'align' => 'right',
                'index' => 'slider_id'
            ]
        );

        $this->addColumn('chooser_title',
            [
                'header' => __('Title'),
                'align' => 'left',
                'index' => 'title'
            ]
        );

        $this->addColumn(
            'type',
            [
                'header' => __('Type'),
                'index' => 'type',
                'type' => 'options',
                'options' => \SuMage\CatalogSlider\Model\Catalogslider::getSliderTypeArray()
            ]
        );

        $this->addColumn(
            'location',
            [
                'header' => __('Location'),
                'index' => 'location',
                'type' => 'options',
                'options' => \SuMage\CatalogSlider\Model\Catalogslider::getSliderGridLocations()
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => \SuMage\CatalogSlider\Model\Catalogslider::getStatusArray()
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('catalogslider/widget/chooser', ['_current' => true]);
    }

}