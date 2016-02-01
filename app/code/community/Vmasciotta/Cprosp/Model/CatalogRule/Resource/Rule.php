<?php

class Vmasciotta_Cprosp_Model_Catalogrule_Resource_Rule extends Mage_CatalogRule_Model_Resource_Rule
{
    /**
     * @inheritdoc
     */
    protected function _getRuleProductsStmt($fromDate, $toDate, $productId = null, $websiteId = null)
    {
        $read = $this->_getReadAdapter();

        /**
         * Sort order is important
         * It used for check stop price rule condition.
         * website_id   customer_group_id   product_id  sort_order
         *  1           1                   1           0
         *  1           1                   1           1
         *  1           1                   1           2
         * if row with sort order 1 will have stop flag we should exclude
         * all next rows for same product id from price calculation
         */
        $select = $read->select();
        //$select->from(null, array('default_price' => new Zend_Db_Expr("CASE WHEN pp_default_special.value THEN pp_default_special.value ELSE pp_default.value END")));
        $select->from(null,array('default_price' => new Zend_Db_Expr('if(pp_default_special.value != pp_default.value, pp_default_special.value, pp_default.value)')));
        $select
            ->from(array('rp' => $this->getTable('catalogrule/rule_product')))
            ->where($read->quoteInto('rp.from_time = 0 or rp.from_time <= ?', $toDate)
                . ' OR ' . $read->quoteInto('rp.to_time = 0 or rp.to_time >= ?', $fromDate))
            ->order(array('rp.website_id', 'rp.customer_group_id', 'rp.product_id', 'rp.sort_order', 'rp.rule_id'));

        if (!is_null($productId)) {
            $select->where('rp.product_id=?', $productId);
        }

        /**
         * Join default price and websites prices to result
         */
        $priceAttr  = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'price');
        $priceTable = $priceAttr->getBackend()->getTable();
        $attributeId= $priceAttr->getId();

        $joinCondition = '%1$s.entity_id=rp.product_id AND (%1$s.attribute_id=' . $attributeId
            . ') and %1$s.store_id=%2$s';

        $select->join(
            array('pp_default'=>$priceTable),
            sprintf($joinCondition, 'pp_default', Mage_Core_Model_App::ADMIN_STORE_ID)
        );

        /**
         * Special Price Join
         */
        $specialPriceAttr  = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'special_price');
        $specialPriceTable = $specialPriceAttr->getBackend()->getTable();
        $specialPriceAttributeId= $specialPriceAttr->getId();
        $joinCondition2 = '%1$s.entity_id=rp.product_id AND (%1$s.attribute_id=' . $specialPriceAttributeId . ')
                                                AND %1$s.store_id=%2$s';
        $select->joinLeft(
            array('pp_default_special'=>$specialPriceTable),
            sprintf($joinCondition2, 'pp_default_special', Mage_Core_Model_App::ADMIN_STORE_ID)
        );

        if ($websiteId !== null) {
            $website  = Mage::app()->getWebsite($websiteId);
            $defaultGroup = $website->getDefaultGroup();
            if ($defaultGroup instanceof Mage_Core_Model_Store_Group) {
                $storeId = $defaultGroup->getDefaultStoreId();
            } else {
                $storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
            }

            $select->joinInner(
                array('product_website' => $this->getTable('catalog/product_website')),
                'product_website.product_id=rp.product_id ' .
                'AND rp.website_id=product_website.website_id ' .
                'AND product_website.website_id='.$websiteId,
                array()
            );

            $tableAlias = 'pp'.$websiteId;
            $fieldAlias = 'website_'.$websiteId.'_price';
            $select->joinLeft(
                array($tableAlias=>$priceTable),
                sprintf($joinCondition, $tableAlias, $storeId),
                array($fieldAlias=>$tableAlias.'.value')
            );
        } else {
            foreach (Mage::app()->getWebsites() as $website) {
                $websiteId  = $website->getId();
                $defaultGroup = $website->getDefaultGroup();
                if ($defaultGroup instanceof Mage_Core_Model_Store_Group) {
                    $storeId = $defaultGroup->getDefaultStoreId();
                } else {
                    $storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
                }

                $tableAlias = 'pp' . $websiteId;
                $fieldAlias = 'website_' . $websiteId . '_price';
                $select->joinLeft(
                    array($tableAlias => $priceTable),
                    sprintf($joinCondition, $tableAlias, $storeId),
                    array($fieldAlias => $tableAlias.'.value')
                );
            }
        }
        return $read->query($select);
    }
}
