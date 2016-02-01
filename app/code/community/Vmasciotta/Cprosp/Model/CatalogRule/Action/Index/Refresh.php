<?php

class Vmasciotta_Cprosp_Model_CatalogRule_Action_Index_Refresh extends Mage_CatalogRule_Model_Action_Index_Refresh
{
    protected function _prepareTemporarySelect(Mage_Core_Model_Website $website)
    {
        /** @var $catalogFlatHelper Mage_Catalog_Helper_Product_Flat */
        $catalogFlatHelper = $this->_factory->getHelper('catalog/product_flat');
        /** @var $eavConfig Mage_Eav_Model_Config */
        $eavConfig = $this->_factory->getSingleton('eav/config');
        $priceAttribute = $eavConfig->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'price');
        $specialPriceAttr = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'special_price');
        $specialPriceTable = $specialPriceAttr->getBackend()->getTable();
        $specialPriceAttributeId = $specialPriceAttr->getId();
        $select = $this->_connection->select()->from(array(
            'rp' => $this->_resource->getTable('catalogrule/rule_product')
        ) , array())->joinInner(array(
            'r' => $this->_resource->getTable('catalogrule/rule')
        ) , 'r.rule_id = rp.rule_id', array())->where('rp.website_id = ?', $website->getId())->order(array(
            'rp.product_id',
            'rp.customer_group_id',
            'rp.sort_order',
            'rp.rule_product_id'
        ))->joinLeft(array(
            'pg' => $this->_resource->getTable('catalog/product_attribute_group_price')
        ) , 'pg.entity_id = rp.product_id AND pg.customer_group_id = rp.customer_group_id' . ' AND pg.website_id = rp.website_id', array())->joinLeft(array(
            'pgd' => $this->_resource->getTable('catalog/product_attribute_group_price')
        ) , 'pgd.entity_id = rp.product_id AND pgd.customer_group_id = rp.customer_group_id' . ' AND pgd.website_id = 0', array());
        $storeId = $website->getDefaultStore()->getId();
        if ($catalogFlatHelper->isEnabled() && $storeId && $catalogFlatHelper->isBuilt($storeId))
        {
            $select->joinInner(array(
                'p' => $this->_resource->getTable('catalog/product_flat') . '_' . $storeId
            ) , 'p.entity_id = rp.product_id', array());
            $priceColumn = $this->_connection->getIfNullSql($this->_connection->getIfNullSql('pg.value', 'pgd.value') , $this->_connection->getIfNullSql('p.special_price', 'p.price'));
        }
        else
        {
            $select->joinInner(array(
                'pd' => $this->_resource->getTable(array(
                    'catalog/product',
                    $priceAttribute->getBackendType()
                ))
            ) , 'pd.entity_id = rp.product_id AND pd.store_id = 0 AND pd.attribute_id = ' . $priceAttribute->getId() , array())->joinLeft(array(
                'pspd' => $specialPriceTable
            ) , 'pspd.entity_id = rp.product_id AND (pspd.attribute_id=' . $specialPriceAttributeId . ')' . 'AND pspd.store_id = 0', array())->joinLeft(array(
                'p' => $this->_resource->getTable(array(
                    'catalog/product',
                    $priceAttribute->getBackendType()
                ))
            ) , 'p.entity_id = rp.product_id AND p.store_id = ' . $storeId . ' AND p.attribute_id = pd.attribute_id', array())->joinLeft(array(
                'psp' => $specialPriceTable
            ) , 'psp.entity_id = rp.product_id AND (psp.attribute_id=' . $specialPriceAttributeId . ')' . 'AND psp.store_id = ' . $storeId, array());
            $priceColumn = $this->_connection->getIfNullSql($this->_connection->getIfNullSql('pg.value', 'pgd.value') , $this->_connection->getIfNullSql('psp.value', $this->_connection->getIfNullSql('pspd.value', $this->_connection->getIfNullSql('p.value', 'pd.value'))));
        }

        $select->columns(array(
            'grouped_id' => $this->_connection->getConcatSql(array(
                'rp.product_id',
                'rp.customer_group_id'
            ) , '-') ,
            'product_id' => 'rp.product_id',
            'customer_group_id' => 'rp.customer_group_id',
            'from_date' => 'r.from_date',
            'to_date' => 'r.to_date',
            'action_amount' => 'rp.action_amount',
            'action_operator' => 'rp.action_operator',
            'action_stop' => 'rp.action_stop',
            'sort_order' => 'rp.sort_order',
            'price' => $priceColumn,
            'rule_product_id' => 'rp.rule_product_id',
            'from_time' => 'rp.from_time',
            'to_time' => 'rp.to_time'
        ));
        return $select;
    }
}