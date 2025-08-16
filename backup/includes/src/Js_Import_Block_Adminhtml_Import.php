<?php

class Js_Import_Block_Adminhtml_Import extends Mage_Adminhtml_Block_Widget
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('js/import/custom-import.phtml');
	}
}