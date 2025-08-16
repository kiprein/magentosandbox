<?php

class Js_Guestwishlist_Block_Share_Email_Items extends Mage_Core_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('js/guestwishlist/share/email/items.phtml');
	}
}