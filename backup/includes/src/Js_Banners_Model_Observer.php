<?php

class Js_Banners_Model_Observer
{
	public function scheduleBanners()
	{
		$currentTime = date('Y-m-d H:i:s');
		$currentTime = date('Y-m-d H:i:s', strtotime($currentTime) - 60 * 60 * 6);

		//Mage::log('Schedule Banners has started');

		$this->activateBanners($currentTime);
		$this->deactivateBanners($currentTime);

		//Mage::log('Schedule Banners has finished');
	}

	/**
	 * Find all banners with the following criteria and activate them
	 * Active set to no
	 * active_from date is not empty
	 * active_from date is less then or equal to the current time
	 */
	public function activateBanners($currentTime)
	{
		$banners = Mage::getModel( 'js_banners/banners' )
		               ->getCollection()
		               ->addFieldToFilter( 'active',
			               array(
				               array( 'eq' => 0 ),
				               array( 'eq' => '' ),
			               )
		               )
		               ->addFieldToFilter( 'active_from', array( 'neq' => '' ) )
		               ->addFieldToFilter( 'active_from',
			               array(
				               array( 'lt' => $currentTime ),
				               array( 'eq' => $currentTime ),
			               )
		               );
		//Mage::log('Banners are being activated at '.$currentTime, null, 'banner.log');

		foreach($banners as $banner) {
			//Mage::log('Banner '.$banner->getId() .' activated', null, 'banner.log');
			$banner->setActive(1)->save();
		}
	}

	/**
	 * Find all banners with the following criteria and deactivate them
	 * Active set to yes 1
	 * active_to date is not empty
	 * active_to date is less then or equal to the current time
	 */
	public function deactivateBanners($currentTime)
	{
		$banners = Mage::getModel( 'js_banners/banners' )
		               ->getCollection()
		               ->addFieldToFilter( 'active',array( 'eq' => 1 ) )
		               ->addFieldToFilter( 'active_to', array( 'neq' => '' ) )
		               ->addFieldToFilter( 'active_to',
			               array(
				               array( 'lt' => $currentTime ),
				               array( 'eq' => $currentTime ),
			               )
		               );

		//Mage::log('Banners are being Deactivated at '.$currentTime, null, 'banner.log');

		foreach($banners as $banner) {
			//Mage::log('Banner '.$banner->getId() .' DEactivated', null, 'banner.log');
			$banner->setActive(0)->save();
		}
	}

}