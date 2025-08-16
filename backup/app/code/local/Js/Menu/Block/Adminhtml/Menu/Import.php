<?php

class Js_Menu_Block_Adminhtml_Menu_Import extends Mage_Adminhtml_Block_Widget {
	public function __construct() {
		parent::__construct();
		$this->setTemplate('js/menu/import.phtml');
	}
}