<?php

class Js_Product_Block_Product_List_Toolbar extends Amasty_Shopby_Block_Catalog_Product_List_Toolbar {

	/**
	 * Get grid products sort order field.  For some reason this is not being reset correctly when no longer sorting
	 *
	 * @return string
	 */
	public function getCurrentOrder()
	{
		$order = $this->_getData('_current_grid_order');
		if ($order) {
			return $order;
		}

		$orders = $this->getAvailableOrders();
		$defaultOrder = $this->_orderField;

		if (!isset($orders[$defaultOrder])) {
			$keys = array_keys($orders);
			$defaultOrder = $keys[0];
		}

		$order = $this->getRequest()->getParam($this->getOrderVarName());
		if ($order && isset($orders[$order])) {
			if ($order == $defaultOrder) {
				Mage::getSingleton('catalog/session')->unsSortOrder();
			} else {
				$this->_memorizeParam('sort_order', $order);
			}
		} else {
			Mage::getSingleton('catalog/session')->unsSortOrder();

			//This was the Magento way to do it but it never cleared out correctly when restarting
			//$order = Mage::getSingleton('catalog/session')->getSortOrder();
		}
		// validate session value
		if (!$order || !isset($orders[$order])) {
			$order = $defaultOrder;
		}
		$this->setData('_current_grid_order', $order);
		return $order;
	}

	public function getCurrentDirection()
	{
		$dir = $this->_getData('_current_grid_direction');
		if ($dir) {
			return $dir;
		}

		$directions = array('asc', 'desc');
		$dir = strtolower($this->getRequest()->getParam($this->getDirectionVarName()));
		if ($dir && in_array($dir, $directions)) {
			if ($dir == $this->_direction) {
				Mage::getSingleton('catalog/session')->unsSortDirection();
			} else {
				$this->_memorizeParam('sort_direction', $dir);
			}
		} else {
			$dir = Mage::getSingleton('catalog/session')->getSortDirection();
		}

		// validate direction
		/**
		 * By default prices sort low to high.  This fixes it
		 */
		if (!$dir || !in_array($dir, $directions) && $this->_direction == 'asc') {
			$dir = 'desc';
		}

		$this->setData('_current_grid_direction', $dir);
		return $dir;
	}

	/**
	 * Set collection to pager
	 * Needed to add in height logic if sorting by name
	 *
	 * @param Varien_Data_Collection $collection
	 * @return Mage_Catalog_Block_Product_List_Toolbar
	 */
	public function setCollection($collection)
	{
		$this->_collection = $collection;

		$this->_collection->setCurPage($this->getCurrentPage());

		// we need to set pagination only if passed value integer and more that 0
		$limit = (int)$this->getLimit();
		if ($limit) {
			$this->_collection->setPageSize($limit);
		}

		if ($this->getCurrentOrder()) {
			//using addAttributeToSort makes sure it gets listed first
			$this->_collection->addAttributeToSort('position', 'ASC');

			if($this->getCurrentOrder() == 'relevance') {
				$this->_collection->setOrder('price', 'DESC');
			} else {
				$this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
			}


			if($this->getCurrentOrder() == 'name') {
				$this->_collection->setOrder('height', $this->getCurrentDirection());
			}

			if($this->getCurrentOrder() == 'price') {
				$this->_collection->setOrder('sku', 'asc');
			}
		}
		return $this;
	}
}