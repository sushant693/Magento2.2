<?php
/**
 * Copyright © 2017 sushant Kumar (sushant693@gmail.com) All rights reserved.
 */

namespace SuMage\CatalogSlider\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 * @package SuMage\CatalogSlider\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    const TABLE_NAME = 'sumage_catalogslider_flat';

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if(version_compare($context->getVersion(), '1.1.1', '<=')){
            $this->addDisplayPrice($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addDisplayPrice(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(self::TABLE_NAME),
            'display_price',
            [   'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => 1,
                'comment' => 'Display product price'
            ]);
        $setup->getConnection()->addColumn(
            $setup->getTable(self::TABLE_NAME),
            'display_cart',
            [   'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => 1,
                'comment' => 'Display add to cart button'
            ]);

        $setup->getConnection()->addColumn(
            $setup->getTable(self::TABLE_NAME),
            'display_wishlist',
            [   'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => 1,
                'comment' => 'Display add to wish list'
            ]);

        $setup->getConnection()->addColumn(
            $setup->getTable(self::TABLE_NAME),
            'display_compare',
            [   'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => 1,
                'comment' => 'Display add to compare'
            ]);

    }

}