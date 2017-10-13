<?php
/**
 * Copyright © 2017 sushant Kumar (sushant693@gmail.com) All rights reserved.
 */

namespace SuMage\CatalogSlider\Block\Slider;

//use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Reports\Block\Product\Widget\Viewed;
//use Magento\Framework\View\Element\Template;
//use SuMage\CatalogSlider\Block\Slider\RecentViewed;
class Items extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Max number of products in slider
     */
    const PRODUCTS_COUNT = 10;

    /**
     * Products collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productsCollectionFactory;
    
    /**
     * Product Index Collection
     *
     * @var \Magento\Reports\Model\ResourceModel\Product\Index\Collection\AbstractCollection
     */
    protected $_collection;

    /**
     * @var \Magento\Reports\Model\Product\Index\Factory
     */
    protected $_indexFactory;
    
     /**
     * Product Index model type
     *
     * @var string
     */
    protected $_indexType;
    
    /**
     * Product reports collection factory
     *
     * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_reportsCollectionFactory;

    /**
     * Product slider factory
     *
     * @var \SuMage\CatalogSlider\Model\CatalogsliderFactory
     */
    protected $_sliderFactory;

    /**
     * Product slider id
     *
     * @var int
     */
    protected $_sliderId;

    /**
     * Product slider model
     *
     * @var \SuMage\CatalogSlider\Model\Catalogslider
     */
    protected $_slider;

    /**
     * Events type factory
     *
     * @var \Magento\Reports\Model\Event\TypeFactory
     */
    protected $_eventTypeFactory;
    protected $_recently;
    public $_recent;
    protected $_abstract;
    /**
     * Products visibility
     *
     * @var \Magento\Reports\Model\Event\TypeFactory
     */
    protected $_catalogProductVisibility;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Product slider template
     */
    protected $_template = 'SuMage_CatalogSlider::slider/items.phtml';

    
    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Reports\Block\Product\Viewed $recently
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $reportsCollectionFactory
     * @param \SuMage\CatalogSlider\Model\CatalogsliderFactory $catalogsliderFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Reports\Model\Event\TypeFactory $eventTypeFactory
     * @param array $data
     */
    public function __construct
    (
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
//        \Magento\Reports\Block\Product\Widget\Viewed $recently,
//        \SuMage\CatalogSlider\Block\Slider\RecentViewed $recent,
//        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsCollectionFactory,
//        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $reportsCollectionFactory,
        \SuMage\CatalogSlider\Model\CatalogsliderFactory $catalogsliderFactory,
        RecentViewed $_recent,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Reports\Model\Event\TypeFactory $eventTypeFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
         Viewed $_recently,
        \Magento\Reports\Model\Product\Index\Factory $indexFactory,
//         AbstractProduct $_abstract,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable,
        array $data = []
    ){
        $this->_productCollectionFactory = $productsCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_reportsCollectionFactory = $reportsCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
//        $this->_resource = $resource;
        $this->_sliderFactory = $catalogsliderFactory;
        $this->_dateTime = $dateTime;
        $this->_eventTypeFactory = $eventTypeFactory;
        $this->_productRepository = $productRepository;
        $this->_catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
        $this->_storeManager = $context->getStoreManager();
        $this->_recently = $_recently;
        $this->recent = $_recent;
        $this->_indexFactory = $indexFactory;
//        $this->_abstract = $_abstract;
        parent::__construct($context,$data);
    }

    
    /**
     * Get product slider items based on type
     */
    public function getSliderProducts()
    {
        $collection = "";
        switch($this->_slider->getType()){
            case 'new':
                $collection =  $this->_getNewProducts($this->_productCollectionFactory->create());
                break;
            case 'bestsellers':
                $collection = $this->_getBestsellersProducts($this->_productCollectionFactory->create());
                break;
            case 'mostviewed':
                $collection =  $this->_getMostViewedProducts($this->_productCollectionFactory->create());
                break;
            case 'onsale':  
                $collection =  $this->_getOnSaleProducts($this->_productCollectionFactory->create());
                break;
            case 'featured':
                $collection =  $this->_getSliderFeaturedProducts($this->_productCollectionFactory->create());
                break;
            case 'specialPrice':
                $collection =  $this->_getSpecialPriceProducts($this->_productCollectionFactory->create());
                break;
            case 'recentlyviewed':
                $collection =  $this->_getRecentViewedProducts();
                break;
        }
        return $collection;
    }

    /**
     * Get additional-featured slider products
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getSliderFeaturedProducts($collection)
    { 
        $collection = $this->_addProductAttributesAndPrices($collection);
        $collection->addAttributeToFilter(array(array('attribute' => 'is_featured', 'eq' => '1')))
                        ->addAttributeToSelect('name')
                    ->addAttributeToSelect('sku')
                    ->addAttributeToSelect('price')
                    ->addStoreFilter($this->getRequest()->getParam('store')
                    );
//                 print_r($collection->getData());die('sds');
//        $productNumber = $this->getProducts_in_slider();   
//        $collection->getSelect()->where('slider_id = '.$this->getSliderId());
//                    ->join(['slider_products' => $collection->getTable('sumage_catalogslider_flat')],
//                            'slider_products.slider_id = '.$this->getSliderId(),
//                            ['position'])
//                    ->order('slider_products.position');
//            print_r($collection->getData());die('sds');
        $collection->addStoreFilter($this->getStoreId())
                    ->setPageSize($this->getProducts_In_Slider())
                    ->setCurPage(1);
            
        return $collection;
    }

    /**
     * Get product slider items based on type
     *
     * Get new listing products
     * 
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getNewProducts($collection)
    {
//        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection);
//         $collection->getSelect()
//                    ->join(['slider_products' => $collection->getTable('sumage_catalogslider_product_flat')],
//                            'e.entity_id = slider_products.product_id AND slider_products.slider_id = '.$this->getSliderId(),
//                            ['position'])
//                    ->order('slider_products.position');
        
        $collection->setOrder('created_at', 'DESC');
                $collection->getSelect()
                            ->limit($this->getProducts_In_Slider());
               // print_r($collection->getData());die('dhfg');
                $i= 0;
                foreach ($collection as $new)
                {
                    $arr[$i] = $new->getEntity_id();
                    $i++;
                }
//                $collection = $this->_productFactory->create()->getCollection();
                $collection = $collection->addIdFilter($arr);
//                print_r($collection->getData());die('dhfg');
                $collection->addAttributeToSelect('name')
                    ->addAttributeToSelect('sku')
                    ->addAttributeToSelect('price')
                    ->addStoreFilter(
                      $this->getRequest()->getParam('store')
                    );
                 
        $collection->addStoreFilter($this->getStoreId())
            ->setPageSize($this->getProducts_In_Slider())
            ->setCurPage(1);
        return $collection;
    }

    /**
     * Get most viewed products
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return Collection
     */
    protected function _getMostViewedProducts($collection)
    {
        $eventTypes = $this->_eventTypeFactory->create()->getCollection();
        $reportCollection = $this->_reportsCollectionFactory->create();

        // Getting event type id for catalog_product_view event
        foreach ($eventTypes as $eventType) {
            if ($eventType->getEventName() == 'catalog_product_view') {
                $productViewEvent = (int)$eventType->getId();
                break;
            }
        }

        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection);
        $collection->getSelect()->reset()->from(
                    ['report_table_views' => $reportCollection->getTable('report_event')],
                    ['views' => 'COUNT(report_table_views.event_id)']
                )->join(
                    ['e' => $reportCollection->getProductEntityTableName()],
                    $reportCollection->getConnection()->quoteInto(
                        'e.entity_id = report_table_views.object_id',
                        $reportCollection->getProductAttributeSetId()
                    )
                )->where(
                    'report_table_views.event_type_id = ?',
                    $productViewEvent
                )->group(
                    'e.entity_id'
                )->order(
                    'views DESC'
                )->having(
                    'COUNT(report_table_views.event_id) > ?',
                    0
                );

        $collection->addStoreFilter($this->getStoreId())
            ->setPageSize($this->getProducts_In_Slider())
            ->setCurPage(1);
//            ->addViewsCount()
        return $collection;
    }

    /**
     * Get most rated slider products
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getOnSaleProducts($collection)
    {
//        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
//        $collection = $this->_addProductAttributesAndPrices($collection);
//         $collection->getSelect()
//                    ->join(['slider_products' => $collection->getTable('sumage_catalogslider_product_flat')],
//                            'e.entity_id = slider_products.product_id AND slider_products.slider_id = '.$this->getSliderId(),
//                            ['position'])
//                    ->order('slider_products.position');
//        die('here');
//        $collection = $this->_ratingOptionVoteF($collection);
//        print_r($collection->getData());die('here');
//        $collection = $this->_addProductAttributesAndPrices($collection);
        $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get('Magento\Framework\App\ResourceConnection');
        $connection = $this->_resources->getConnection();
        $dbcollection = $connection->fetchAll("SELECT * FROM `rating_option_vote_aggregated` GROUP BY `entity_pk_value` ORDER BY `percent_approved` DESC LIMIT ".$this->getProducts_In_Slider()."");
       $i= 0;
//       print_r($collection);die('here');
                foreach ($dbcollection as $mostRated)
                {
//                                        print_r($mostRated['entity_pk_value']);die('hh');
                    $arr[$i] = $mostRated['entity_pk_value'];
                    $i++;
                }
                $collection = $this->_addProductAttributesAndPrices($collection);
//                print_r($collection->getData());die('sdh');
                $collection = $collection->addIdFilter($arr);
//                foreach ($arr as $id) {
//                    foreach ($collection->getData() as $value) {
//                    
//                        if($value['entity_id'] == $id){
//                            $collection1[] = $value;
//                        }
//                    }
//                }
                $collection->addAttributeToSelect('name')
                    ->addAttributeToSelect('sku')
                    ->addAttributeToSelect('price')
                    ->addStoreFilter(
                      $this->getRequest()->getParam('store')
                    );
                 
        $collection->addStoreFilter($this->getStoreId())
            ->setPageSize($this->getProducts_In_Slider())
            ->setCurPage(1);
        return $collection;
    }

    /**
     * Get best selling products
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getBestsellersProducts($collection)
    {
//        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
       
        $collection = $this->_addProductAttributesAndPrices($collection);
        $collection->getSelect()
                    ->join(['bestsellers' => $collection->getTable('sales_bestsellers_aggregated_yearly')],
                                'e.entity_id = bestsellers.product_id AND bestsellers.store_id = '.$this->getStoreId(),
                                ['qty_ordered','rating_pos'])
                    ->order('rating_pos');
//        $Products_in_slider = $this->getProducts_In_Slider();
        $collection->addStoreFilter($this->getStoreId())
                    ->setPageSize($this->getProducts_In_Slider())
                    ->setCurPage(1);
        
//            print_r($coll);die('sds');
        return $collection;
    }

    /**
     * Get special price offer products
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getSpecialPriceProducts($collection)
    {
//        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection);
         $collection->addAttributeToSelect('special_price');
         $i = 0;
                foreach ($collection as $value) {
                        if($value->getSpecial_price() > 0)
                        {
                            $arr[$i] = $value->getEntity_id();
                            $i++;
                        }
                }
//                print_r($arr);die('hee');
                $collection = $collection->addIdFilter($arr);
        $collection->addStoreFilter($this->getStoreId())
            ->setPageSize($this->getProducts_In_Slider())
            ->setCurPage(1);
        return $collection;
    }
    
    
    /**
     * Get recently viewed products
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getRecentViewedProducts()
    {
        
        if ($this->_collection === null) {
            
            $attributes = $this->_catalogConfig->getProductAttributes();

            $this->_collection = $this->getModel()->getCollection()->addAttributeToSelect($attributes);

            if ($this->getCustomerId()) {
                $this->_collection->setCustomerId($this->getCustomerId());
            }
            $this->_collection->excludeProductIds(
                $this->getModel()->getExcludeProductIds()
            );

            /* Price data is added to consider item stock status using price index */
            $this->_collection->addPriceData();

            $ids = $this->getProductIds();
            if (empty($ids)) {
                $this->_collection->addIndexFilter();
            } else {
                $this->_collection->addFilterByIds($ids);
            }
            $this->_collection->setAddedAtOrder()->setVisibility($this->_catalogProductVisibility->getVisibleInSiteIds());
        }
//        $collection = $this->_collection;
                $this->_collection->addStoreFilter($this->getStoreId())
                    ->setPageSize($this->getProducts_In_Slider())
                    ->setCurPage(1);
//        print_r($this->_collection->getData());
//        die('sd');
        return $this->_collection;
    }
    
    /**
     * Get slider products including additional products
     *
     * @return array
     */
    public function getSliderProductsCollection()
    {
        $collection = [];
        $featuredProducts = $this->getSliderFeaturedProducts();
        $sliderProducts = $this->getSliderProducts();
        if(count($featuredProducts)>0){
            $collection['featured'] = $featuredProducts;
        }

        if(count($sliderProducts)>0){
            $collection['products'] = $sliderProducts;
        }
        return $collection;
    }

    /**
     * Get start of day date
     *
     * @return string
     */
    public function getStartOfDayDate()
    {
        return $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
    }

    /**
     * Get end of day date
     *
     * @return string
     */
    public function getEndOfDayDate()
    {
        return $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
    }

    /**
     * Set slider model
     *
     * @param \SuMage\CatalogSlider\Model\Catalogslider $slider
     *
     * @return this
     */
    public function setSlider($slider)
    {
        $this->_slider = $slider;
        return $this;
    }

    /**
     * Get slider id
     *
     * @return int
     */
    public function getSliderId()
    {
        return $this->_slider->getId();
    }

    /**
     * Get Products in slider
     *
     * @return int
     */
    public function getProducts_In_Slider()
    {
        return $this->_slider->getProducts_in_slider();
    }
    
    /**
     * Get slider
     *
     * @return \SuMage\CatalogSlider\Model\Catalogslider
     */
    public function getSlider()
    {
        return $this->_slider;
    }

    /**
     * @param int
     *
     * @return this
     */
    public function setSliderId($sliderId)
    {
        $this->_sliderId = $sliderId;
        $slider = $this->_sliderFactory->create()->load($sliderId);

        if($slider->getId()){
            $this->setSlider($slider);
            $this->setTemplate($this->_template);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSliderDisplayId()
    {
        return $this->_dateTime->timestamp().$this->getSliderId();
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return int
     */
    public function getProductsCount()
    {
        return self::PRODUCTS_COUNT;
    }
    
    /*
     * @param int which is productId
     * @return string of productURL
     */
    public function parentURL($id) {
        $parentByChild = $this->_catalogProductTypeConfigurable->getParentIdsByChild($id);
            if(isset($parentByChild[0])){
                //set id as parent product id...
                $id = $parentByChild[0];
            }
//            return $id;
        $product = $this->_productRepository->getById($id);
        return $product->getUrlModel()->getUrl($product);
    }
    
    /**
     * Public method for retrieve Product Index model
     *
     * @return \SuMage\CatalogSlider\Block\Slider\Items
     */
     public function getModel()
    {
        try {
            $model = $this->_indexFactory->get("viewed");
        } catch (\InvalidArgumentException $e) {
            new \Magento\Framework\Exception\LocalizedException(__('Index type is not valid'));
        }
        return $model;
    }
}