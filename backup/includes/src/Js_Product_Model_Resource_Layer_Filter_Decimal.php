<?php

class Js_Product_Model_Resource_Layer_Filter_Decimal extends Amasty_Shopby_Model_Mysql4_Decimal
{
	/**
	 * Retrieve array with products counts per range
	 *
	 * Add group by reset
	 *
	 * @param Mage_Catalog_Model_Layer_Filter_Decimal $filter
	 * @param int $range
	 * @return array
	 */
	public function getCount($filter, $range)
	{
		$select     = $this->_getSelect($filter);
		$adapter    = $this->_getReadAdapter();

		$countExpr  = new Zend_Db_Expr("COUNT(*)");
		$rangeExpr  = new Zend_Db_Expr("FLOOR(decimal_index.value / {$range}) + 1");

		$select->columns(array(
			'decimal_range' => $rangeExpr,
			'count' => $countExpr
		));

		//When doing the inventory the group is all messed up so you need to reset it
		$currentGroups = $select->getPart(Zend_Db_Select::GROUP);
		if(isset($currentGroups[0]) && $currentGroups[0] == 'e.entity_id') {
			$select->reset(Zend_Db_Select::GROUP);
		}

		$select->group($rangeExpr);

		return $adapter->fetchPairs($select);
	}
}