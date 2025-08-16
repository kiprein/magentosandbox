<?php

class Ravi_Testimonials_Block_Adminhtml_Testimonial_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm() {

		$form = new Varien_Data_Form();
		$this->setForm( $form );
		$fieldset = $form->addFieldset( "testimonials_form", array( "legend" => Mage::helper( "testimonials" )->__( "Item information" ) ) );


		$fieldset->addField( 'status', 'select', array(
			'label'  => Mage::helper( 'testimonials' )->__( 'Active' ),
			'values' => array('0' => 'No','1' => 'Yes'),
			'name'   => 'status',
		) );


		$fieldset->addField( "phonenumber", "text", array(
			"label" => Mage::helper( "testimonials" )->__( "Phone Number" ),
			"name"  => "phonenumber",
		) );


		$fieldset->addField( 'photo', 'image', array(
			'label' => Mage::helper( 'testimonials' )->__( 'Photo' ),
			'name'  => 'photo',
			'note'  => '(*.jpg, *.png, *.gif)',
		) );
		$fieldset->addField( "video", "textarea", array(
			"label" => Mage::helper( "testimonials" )->__( "Video" ),
			"name"  => "video",
		) );

		$fieldset->addField( "comments", "editor", array(
			"label"   => Mage::helper( "testimonials" )->__( "Comments" ),
			"class"   => "required-entry",
			"name"    => "comments",
			'wysiwyg' => true,
			'config'  => Mage::getSingleton( 'cms/wysiwyg_config' )->getConfig()
		) );


		if ( Mage::getSingleton( "adminhtml/session" )->getTestimonialData() ) {
			$form->setValues( Mage::getSingleton( "adminhtml/session" )->getTestimonialData() );
			Mage::getSingleton( "adminhtml/session" )->setTestimonialData( null );
		} elseif ( Mage::registry( "testimonial_data" ) ) {
			$form->setValues( Mage::registry( "testimonial_data" )->getData() );
		}

		return parent::_prepareForm();
	}
}
