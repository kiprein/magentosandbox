<?php
/**
 * Created by PhpStorm.
 * User: Jon Saverda
 * Date: 3/27/2017
 * Time: 12:09 PM
 */
class Js_Guestwishlist_Adminhtml_WishlistController extends Mage_Adminhtml_Controller_Action {

	protected function _initAction()
	{
		$this->loadLayout()
		     ->_addBreadcrumb(Mage::helper('adminhtml')->__('Customers Wishlist Collection'), Mage::helper('adminhtml')->__('Customers Wishlist Collection'));
		return $this;
	}

	public function generalAction() {
		$this->_initAction();
		$this->_addContent($this->getLayout()->createBlock('js_guestwishlist/adminhtml_general_user'));
		$this->renderLayout();
	}

	public function guestsAction() {
		$this->_initAction();
		$this->_addContent($this->getLayout()->createBlock('js_guestwishlist/adminhtml_guest_user'));
		$this->renderLayout();
	}

	public function exportGuestsAction() {
		$filename = 'guest-users-wishlists.csv';
		$content = Mage::helper('js_guestwishlist/export')->generateWishlistExport('guest', $filename);

		$this->_prepareDownloadResponse($filename, $content, 'text/csv');
	}

	public function exportGeneralAction() {
		$filename = 'general-users-wishlists.csv';
		$content = Mage::helper('js_guestwishlist/export')->generateWishlistExport('general', $filename);

		$this->_prepareDownloadResponse($filename, $content, 'text/csv');
	}
}