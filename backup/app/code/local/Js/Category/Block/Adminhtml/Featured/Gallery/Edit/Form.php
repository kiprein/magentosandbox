<?php
/**
 * Created by PhpStorm.
 * User: fab_5
 * Date: 8/25/2018
 * Time: 6:50 AM
 */
class Js_Category_Block_Adminhtml_Featured_Gallery_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _getModel(){
        return Mage::registry('current_model');
    }

    protected function _getHelper(){
        return Mage::helper('js_category');
    }

    protected function _getModelTitle(){
        return 'Featured Gallery';
    }

    protected function _prepareForm()
    {
        $model  = $this->_getModel();
        $modelTitle = $this->_getModelTitle();
        $form   = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save'),
            'method'    => 'post',
            'enctype' => 'multipart/form-data',
        ));

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => $this->_getHelper()->__("$modelTitle Information"),
            'class'     => 'fieldset',
        ));

        if ($model && $model->getId()) {
            $modelPk = $model->getResource()->getIdFieldName();
            $fieldset->addField($modelPk, 'hidden', array(
                'name' => $modelPk,
            ));
        }

	    $fieldset->addField('title', 'text', array(
		    'name'     => 'title',
		    'label'    => Mage::helper('js_category')->__('Title'),
		    'required' => false,
		    'style' => 'width: 500px',
	    ));

	    /**
	     * This field should not be part of the banner array otherwise the method that saves info to the
	     * db will break
	     */
	    $fieldset->addType('image', 'Js_Category_Block_Adminhtml_Template_Form_Renderer_Image');
	    $fieldset->addField('image', 'image', array(
		    'name'     => 'image',
		    'label'    => Mage::helper('js_category')->__('Image'),
		    'title'    => Mage::helper('js_category')->__('Image'),
		    'required' => false,
	    ));

	    $fieldset->addField('link', 'text', array(
		    'name'     => 'link',
		    'label'    => Mage::helper('js_category')->__('Link'),
		    'required' => false,
		    'style' => 'width: 500px',
	    ));

	    $fieldset->addField('sort_order', 'text', array(
		    'name'     => 'sort_order',
		    'label'    => Mage::helper('js_category')->__('Sort Order'),
		    'required' => false,
		    'style' => 'width: 30px',
	    ));

	    $fieldset->addField('active', 'select', array(
		    'name'   => 'active',
		    'label'  => Mage::helper('js_category')->__('Active'),
		    'value'  => '1',
		    'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray()
	    ));


	    if($model){
            $form->setValues($model->getData());
        }
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
