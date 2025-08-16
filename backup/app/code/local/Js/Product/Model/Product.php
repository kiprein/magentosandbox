<?php

class Js_Product_Model_Product extends Mage_Catalog_Model_Product {
	/**
	 * Retrieve collection related product
	 *
	 * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
	 *
	 * Rewrote to fix the order by to use sku and hide offline and private
	 */
	public function getRelatedProductCollection($fields = null) {
		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
		if(isset($fields)){
			$collection = $this->getLinkInstance()
			->useRelatedLinks()
			->getProductCollection()
			->addAttributeToSelect($fields)
			->setIsStrongMode();
		} else {
			$collection = $this->getLinkInstance()
			->useRelatedLinks()
			->getProductCollection()
			->setIsStrongMode();
		}
		

		if($groupId == 6) {
			$collection->addAttributeToFilter('b_private', array('IN' => array(0, 1)));
			$collection->addAttributeToFilter('b_offline', array('eq' => 0));
		} else {
			$collection->addAttributeToFilter('b_private', array('eq' => 0));
			$collection->addAttributeToFilter('b_offline', array('eq' => 0));
		}


		$collection->setProduct($this);

		//This only hides these if the user is not an employee or viewing from the admin

//		if($groupId != 6 && !Mage::app()->getStore()->isAdmin()) {
//			$collection->addAttributeToFilter('b_offline', array('eq' =>0));
//			$collection->addAttributeToFilter('b_private', array('eq' =>0));
//		}

		$collection->setOrder('sku', 'ASC');
		return $collection;
	}

	/**
	 * Retrieve collection cross sell product
	 *
	 * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Link_Product_Collection
	 *
	 * Rewrote to hide offline and private
	 */
	public function getCrossSellProductCollection()
	{
		$collection = $this->getLinkInstance()->useCrossSellLinks()
		                   ->getProductCollection()
		                   ->setIsStrongMode();
		$collection->setProduct($this);

		$collection->addAttributeToFilter('b_offline', array('eq' =>0));
		$collection->addAttributeToFilter('b_private', array('eq' =>0));

		return $collection;
	}
}