<?php

class Js_Menu_Block_Adminhtml_Menu_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
	protected function _prepareForm() {
		$form = new Varien_Data_Form();
		$this->setForm( $form );
		$fieldset = $form->addFieldset( 'menu_form', array( 'legend' => Mage::helper( 'js_menu' )->__( 'Item information' ) ) );

		$fieldset->addField('title', 'text', array(
			'name'     => 'menu[title]',
			'label'    => Mage::helper('js_menu')->__('Title'),
			'title'    => Mage::helper('js_menu')->__('Title'),
			'required' => false,
		));

		$fieldset->addField('menu_type', 'multiselect', array(
				'name'     => 'menu[menu_type]',
				'label'    => Mage::helper('js_menu')->__('Menu Type'),
				'title'    => Mage::helper('js_menu')->__('Menu Type'),
				'required' => false,
				'values'   => array(
					array('value' => 'top', 'label' => 'Top Menu'),
					array('value' => 'main-nav', 'label' => 'Main Nav'),
					array('value' => 'sticky-nav', 'label' => 'Sticky Nav')),
				'style'    => 'height: 90px'
		));

		$fieldset->addField('parent_id', 'select', array(
				'name'     => 'menu[parent_id]',
				'label'    => Mage::helper('js_menu')->__('Parent'),
				'title'    => Mage::helper('js_menu')->__('Parent'),
				'required' => false,
				'values'   => Mage::helper('js_menu')->getMenuOptions(),
		));

		$fieldset->addField('position', 'text', array(
			'name'     => 'menu[position]',
			'label'    => Mage::helper('js_menu')->__('Position'),
			'title'    => Mage::helper('js_menu')->__('Position'),
			'required' => false,
			'style'    => 'width: 30px'
		));

		$fieldset->addField('url', 'text', array(
			'name'     => 'menu[url]',
			'label'    => Mage::helper('js_menu')->__('Url'),
			'title'    => Mage::helper('js_menu')->__('Url'),
			'required' => false,
			'style'    => 'width: 500px',
			'after_element_html' => '<small><em>This needs to be the full url path. Ex: https://www.crystal-d.com/</em></small>'
		));

		$fieldset->addField('target', 'select', array(
			'name'     => 'menu[target]',
			'label'    => Mage::helper('js_menu')->__('On Click'),
			'title'    => Mage::helper('js_menu')->__('On Click'),
			'required' => false,
			'values'   => array('_self' => 'Same Tab / Window', '_blank' => 'New Tab / Window'),
			'after_element_html' => '<br><small><em>This is dependent on the users browsers settings</em></small>'
		));

		$fieldset->addField('class', 'text', array(
			'name'     => 'menu[class]',
			'label'    => Mage::helper('js_menu')->__('Class'),
			'title'    => Mage::helper('js_menu')->__('Class'),
			'required' => false,
		));

		$fieldset->addField( 'active', 'select', array(
			'name'   => 'menu[active]',
			'label'  => Mage::helper( 'js_menu' )->__( 'Active' ),
			'value'  => '1',
			'values' => Mage::getSingleton( 'adminhtml/system_config_source_yesno' )->toArray()
		) );

		if ( Mage::getSingleton( 'adminhtml/session' )->getMenuItemData() ) {
			$form->setValues( Mage::getSingleton( 'adminhtml/session' )->getMenuItemData() );
			Mage::getSingleton( 'adminhtml/session' )->getMenuItemData( null );
		} elseif ( Mage::registry( 'menu_item_data' ) ) {
			$formData = Mage::registry( 'menu_item_data' )->getData();

			//The menu type needs to be formatted correctly
			$formData['menu_type'] = json_decode($formData['menu_type']);
			$form->setValues( $formData);
		}

		return parent::_prepareForm();
	}
}