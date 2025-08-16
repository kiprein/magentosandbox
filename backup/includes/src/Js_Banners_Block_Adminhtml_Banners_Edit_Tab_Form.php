<?php

class Js_Banners_Block_Adminhtml_Banners_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm( $form );
		$fieldset = $form->addFieldset( 'banners_form', array( 'legend' => Mage::helper( 'js_banners' )->__( 'Item information' ) ) );

		$fieldset->addType('image', 'Js_Banners_Block_Adminhtml_Template_Form_Renderer_Image');

		//This field should not be part of the banner array otherwise the method that saves info to the
		//db will break
		$fieldset->addField( 'image', 'image', array(
			'name'     => 'image',
			'label'    => Mage::helper( 'js_banners' )->__( 'Image' ),
			'title'    => Mage::helper( 'js_banners' )->__( 'Image' ),
			'required' => false,
		) );

		$fieldset->addField( 'link', 'text', array(
			'name'     => 'banner[link]',
			'label'    => Mage::helper( 'js_banners' )->__( 'Link' ),
			'required' => false,
			'after_element_html' => '<br><small><em>This will be used if the banner text is left empty.</em></small>',
		) );

		$fieldset->addField('banner_text', 'editor', array(
			'name'     => 'banner[banner_text]',
			'label'    => Mage::helper('js_banners')->__('Banner Text'),
			'required' => false,
			'wysiwyg'  => true,
			'style' => 'height: 400px;width:850px;'
		));

		$fieldset->addField( 'sort_order', 'text', array(
			'name'     => 'banner[sort_order]',
			'label'    => Mage::helper( 'js_banners' )->__( 'Sort Order' ),
			'required' => false,
		) );

		$fieldset->addField( 'active', 'select', array(
			'name'   => 'banner[active]',
			'label'  => Mage::helper( 'js_banners' )->__( 'Active' ),
			'value'  => '1',
			'values' => Mage::getSingleton( 'adminhtml/system_config_source_yesno' )->toArray()
		) );

		if ( Mage::getSingleton( 'adminhtml/session' )->getBannersData() ) {
			$form->setValues( Mage::getSingleton( 'adminhtml/session' )->getBannersData() );
			Mage::getSingleton( 'adminhtml/session' )->setBannersData( null );
		} elseif ( Mage::registry( 'banners_data' ) ) {
			$form->setValues( Mage::registry( 'banners_data' )->getData() );
		}

		return parent::_prepareForm();
	}
}