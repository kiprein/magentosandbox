<?php

class Ravi_Testimonials_Block_Adminhtml_Testimonial_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
	public function __construct() {
		parent::__construct();
		$this->setId( "testimonial_tabs" );
		$this->setDestElementId( "edit_form" );
		$this->setTitle( Mage::helper( "testimonials" )->__( "Item Information" ) );
	}

	protected function _beforeToHtml() {
		$this->addTab( "form_section", array(
			"label"   => Mage::helper( "testimonials" )->__( "Item Information" ),
			"title"   => Mage::helper( "testimonials" )->__( "Item Information" ),
			"content" => $this->getLayout()->createBlock( "testimonials/adminhtml_testimonial_edit_tab_form" )->toHtml(),
		) );

		return parent::_beforeToHtml();
	}

}
