<?php
/**
 * Copyright © 2017 sushant Kumar (sushant693@gmail.com) All rights reserved.
 */

namespace SuMage\CatalogSlider\Block\Adminhtml\Slider\Edit\Tab;
//use \Magento\Catalog\Block\Product\NewProduct;
class Products extends \Magento\Backend\Block\Widget\Grid\Extended {

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
    protected $_indexType = \Magento\Reports\Model\Product\Index\Factory::TYPE_VIEWED;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Reports\Block\Product\Viewed $recently
     * @param \Magento\Backend\Helper\Data $helper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Reports\Block\Product\Viewed $recently,
//        \Magento\Catalog\Block\Product\NewProduct $newProduct,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $bestProduct,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Backend\Helper\Data $helper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,        
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $productsFactory,
        \Magento\Review\Model\ReviewFactory $review,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Review\Model\Review\SummaryFactory $ratingOptionVoteF,
        \SuMage\CatalogSlider\Model\ResourceModel\Product\CollectionFactory $mostViewed,
        array $data = []
    ){
        $this->_productFactory = $productFactory;
//        $this->_newProduct = $newProduct;
        $this->_coreRegistry = $coreRegistry;
        $this->_resource = $resource;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_bestProduct = $bestProduct;
        $this->_productRepository = $productRepository;
        $this->_productsFactory = $productsFactory;
        $this->_review = $review;
        $this->_ratingFactory = $ratingFactory;
        $this->_ratingOptionVoteF = $ratingOptionVoteF;
        $this->_mostViewed = $mostViewed;
        $this->_recently = $recently;
        parent::__construct($context, $helper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('products_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->_aggregateTable = $this->getTable('rating_option_vote_aggregated');
//        $this->setReviewId($this->_coreRegistry->registry('review_data')->getId());
    }

    /**
     * Retrieve product slider object
     *
     * @return \SuMage\CatalogSlider\Model\Catalogslider
     */
    public function getSlider()
    {
        return $this->_coreRegistry->registry('product_slider');
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     *
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in slider flag
        if ($column->getId() == 'in_slider') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {

        if ($this->getSlider()->getSliderId()) {
            $this->setDefaultFilter(['in_slider' => 1]);
        }
        $type = $this->getSlider()->getData('type');
        switch ($type)
        {
            case 'bestsellers':
                $collection = $this->_bestProduct->create()->setModel('Magento\Catalog\Model\Product');
                $i= 0;
                foreach ($collection as $bestseller)
                {
                    $arr[$i] = $bestseller->getProduct_id();
                    $i++;
                }
                $collection = $this->_productFactory->create()->getCollection();
                $collection = $collection->addIdFilter($arr);
                $collection->addAttributeToSelect('name')
                    ->addAttributeToSelect('sku')
                    ->addAttributeToSelect('price')
                    ->addStoreFilter(
                      $this->getRequest()->getParam('store')
                    )
                    ->joinField(
                        'position',
                        'sumage_catalogslider_product_flat',
                        'position',
                        'product_id=entity_id',
                        'slider_id=' . (int)$this->getRequest()->getParam('id', 0),
                        'left'
                    );
                $this->setCollection($collection);
//                print_r($collection->getData());die();
                return parent::_prepareCollection();
                break; 
                
            case 'featured':
                $collection = $this->_productFactory->create()->getCollection();
//                $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
                $collection->addAttributeToFilter(array(array('attribute' => 'is_featured', 'eq' => '1')))
                        ->addAttributeToSelect('name')
                    ->addAttributeToSelect('sku')
                    ->addAttributeToSelect('price')
                    ->addStoreFilter(
                      $this->getRequest()->getParam('store')
                    )
                    ->joinField(
                        'position',
                        'sumage_catalogslider_product_flat',
                        'position',
                        'product_id=entity_id',
                        'slider_id=' . (int)$this->getRequest()->getParam('id', 0),
                        'left'
                    );
                $this->setCollection($collection);
                return parent::_prepareCollection();
                break;
            
            case 'mostviewed':
                $collection = $this->_mostViewed->create()->addAttributeToSelect('*');
                $collection->addViewsCount();
                $this->setCollection($collection);
                return parent::_prepareCollection();
                break;
            
            
            /*
             * use for most rated products.
             */
            case 'onsale':
                    $collection = $this->_ratingOptionVoteF->create()->getCollection();
                    $collection->getSelect()->group('entity_pk_value');
                    $collection->setOrder('rating_summary', 'DESC')->setPageSize(30);
                    // print_r($collection->getData());die();
//                $collection = $this->_review->create()->getCollection();
//                    $collection->getSelect()->group('entity_pk_value');
                $i= 0;
                foreach ($collection as $mostRated)
                {
                    $arr[$i] = $mostRated->getEntity_pk_value();
                    $i++;
                }
                $collection = $this->_productFactory->create()->getCollection();
                $collection = $collection->addIdFilter($arr);
                $collection->addAttributeToSelect('name')
                    ->addAttributeToSelect('sku')
                    ->addAttributeToSelect('price')
                    ->addStoreFilter(
                      $this->getRequest()->getParam('store')
                    )
                    ->joinField(
                        'position',
                        'sumage_catalogslider_product_flat',
                        'position',
                        'product_id=entity_id',
                        'slider_id=' . (int)$this->getRequest()->getParam('id', 0),
                        'left'
                    );
                $this->setCollection($collection);
                return parent::_prepareCollection();
                break;
                
            case 'new':
                $collection = $this->_productFactory->create()->getCollection();
                $collection->setOrder('created_at', 'DESC');
                $collection->getSelect()
                            ->limit(20);
               // print_r($collection->getData());die('dhfg');
                $i= 0;
                foreach ($collection as $new)
                {
                    $arr[$i] = $new->getEntity_id();
                    $i++;
                }
                $collection = $this->_productFactory->create()->getCollection();
                $collection = $collection->addIdFilter($arr);
//                print_r($collection->getData());die('dhfg');
                $collection->addAttributeToSelect('name')
                    ->addAttributeToSelect('sku')
                    ->addAttributeToSelect('price')
                    ->addStoreFilter(
                      $this->getRequest()->getParam('store')
                    )
                    ->joinField(
                        'position',
                        'sumage_catalogslider_product_flat',
                        'position',
                        'product_id=entity_id',
                        'slider_id=' . (int)$this->getRequest()->getParam('id', 0),
                        'left'
                    );
//                print_r($collection->getData());die('dhfg');
                $this->setCollection($collection);
                return parent::_prepareCollection();
                break;
                
            case 'specialPrice':
                $collection = $this->_productFactory->create()->getCollection();
                $collection->addAttributeToSelect('special_price');
//                           ->addAttributeToSelect('special_from_date')
//                           ->addAttributeToSelect('special_to_date');
                $i = 0;
                foreach ($collection as $value) {
//                    print_r($value->getData());
                        if($value->getSpecial_price() > 0)
                        {
                            $arr[$i] = $value->getEntity_id();
                            $i++;
                        }
                }
                $collection = $collection->addIdFilter($arr);
//                print_r($collection->getData());die('dhfg');
                $collection->addAttributeToSelect('name')
                    ->addAttributeToSelect('sku')
                    ->addAttributeToSelect('price')
                    ->addStoreFilter(
                      $this->getRequest()->getParam('store')
                    )
                    ->joinField(
                        'position',
                        'sumage_catalogslider_product_flat',
                        'position',
                        'product_id=entity_id',
                        'slider_id=' . (int)$this->getRequest()->getParam('id', 0),
                        'left'
                    );
//                print_r($collection->getData());die('dhfg');
                $this->setCollection($collection);
                return parent::_prepareCollection();
                break;
                
            case 'recentlyviewed':
//                $object_manager = Magento\Core\Model\ObjectManager::getInstance();
//                $collection = $object_manager->get('\Magento\Reports\Block\Product\Viewed');
//                $collection =$this->_recently->_toHtml();
//                $collection = $this->_mostViewed->create()->getCollection();
                
//        var_dump($collection);
//                foreach ($collection as $value){
//                var_dump($value);die('hh');
//                }
//                die('here');
                break;
                
//            default:
//                $collection = $this->_productFactory->create()->getCollection();
//                $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
//                $collection->addAttributeToSelect('name')
//                    ->addAttributeToSelect('sku')
//                    ->addAttributeToSelect('price')
//                    ->addStoreFilter(
//                      $this->getRequest()->getParam('store')
//                    )
//                    ->joinField(
//                        'position',
//                        'sumage_catalogslider_product_flat',
//                        'position',
//                        'product_id=entity_id',
//                        'slider_id=' . (int)$this->getRequest()->getParam('id', 0),
//                        'left'
//                    );
//                
//                $this->setCollection($collection);
//                
//                return parent::_prepareCollection();
//                break;
        }
    }
    
    
    public function addViewsCount($from = '', $to = '')
    {
        /**
         * Getting event type id for catalog_product_view event
         */
        $eventTypes = $this->_eventTypeFactory->create()->getCollection();
        foreach ($eventTypes as $eventType) {
            if ($eventType->getEventName() == 'catalog_product_view') {
                $productViewEvent = (int)$eventType->getId();
                break;
            }
        }

        $this->getSelect()->reset()->from(
            ['report_table_views' => $this->getTable('report_event')],
            ['views' => 'COUNT(report_table_views.event_id)']
        )->join(
            ['e' => $this->getProductEntityTableName()],
            $this->getConnection()->quoteInto(
                'e.entity_id = report_table_views.object_id AND e.attribute_set_id != ?',
                '0'
            )
        )->where(
            'report_table_views.event_type_id = ?',
            $productViewEvent
        )->group(
            'e.entity_id'
        )->order(
            'views ' . self::SORT_ORDER_DESC
        )->having(
            'COUNT(report_table_views.event_id) > ?',
            0
        );

        if ($from != '' && $to != '') {
            $this->getSelect()->where('logged_at >= ?', $from)->where('logged_at <= ?', $to);
        }
        return $this;
    }
    
    public function getRatings()
    {
       return $this->_ratingFactory->create()->getResourceCollection()->addEntityFilter(
            'product'
        )->setPositionOrder()->addRatingPerStoreName(
            $this->_storeManager->getStore()->getId()
        )->setStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->setActiveFilter(
            true
        )->load()->addOptionToItems()->getData();
    }
    /*
     * used for get product details based on product_id.
     * @param pass the product_id.
     * @return collection of a specific product with image.
     */
    public function productDetail($id){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');

        $collection = $productCollection->create()
                        ->addAttributeToSelect('*')
                       ->addAttributeToSelect('name')
                       ->addAttributeToSelect('image');
        $collection->addAttributeToFilter('entity_id', $id);
        
        return $collection;
    }

    /**
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'in_slider',
            [
                'type' => 'checkbox',
                'name' => 'in_slider',
                'values' => $this->_getSelectedProducts(),
                'index' => 'entity_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );

        $this->addColumn(
             'entity_id',
             [
                 'header' => __('ID'),
                 'sortable' => true,
                 'index' => 'entity_id',
                 'header_css_class' => 'col-id',
                 'column_css_class' => 'col-id'
             ]
         );

         $this->addColumn(
             'name',
             [
                 'header' => __('Name'),
                 'index' => 'name'
             ]);

         $this->addColumn(
             'sku',
             [
                 'header' => __('SKU'),
                 'index' => 'sku'
             ]);

         $this->addColumn(
             'price',
             [
                 'header' => __('Price'),
                 'type' => 'currency',
                 'currency_code' => (string)$this->_scopeConfig->getValue(
                     \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                     \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                 ),
                 'index' => 'price'
             ]
         );

        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'type' => 'number',
                'index' => 'position',
                'editable' => true
            ]
        );

        return parent::_prepareColumns();

    }

    /**
     * @return array
     */
    public function getSelectedSliderProducts()
    {
        $slider_id = $this->getRequest()->getParam('id');

        $select = $this->_resource->getConnection()->select()->from(
            'sumage_catalogslider_product_flat',
            ['product_id', 'position']
        )->where(
            'slider_id = :slider_id'
        );
        $bind = ['slider_id' => (int)$slider_id];

        return $this->_resource->getConnection()->fetchPairs($select, $bind);

    }

    /**
     * @return array|mixed
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getParam('selected_products');
        if ($products === null)  {
            $products = $this->getSlider()->getSelectedSliderProducts();
            return array_keys($products);
        }
        return $products;
    }

    /**
     * Retrieve grid reload url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsgrid', ['_current' => true]);
    }

}