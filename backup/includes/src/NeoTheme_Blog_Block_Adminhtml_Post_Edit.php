<?php

/**
 * NeoTheme (Neo Industries Pty Ltd)
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to Neo Industries Pty LTD Non-Distributable Software Modification License (NDSML)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.neotheme.com/legal/licenses/NDSM.html
 * If the license is not included with the package or for any other reason, 
 * you did not receive your licence please send an email to 
 * license@neotheme.com so we can send you a copy immediately.
 *
 * This software comes with no warrenty of any kind. By Using this software, the user agrees to hold 
 * Neo Industries Pty Ltd harmless of any damage it may cause.
 *
 * @category    modules
 * @module      NeoTheme_Blog
 * @copyright   Copyright (c) 2011 Neo Industries Pty Ltd (http://www.neotheme.com)
 * @license     http://www.neotheme.com/  Non-Distributable Software Modification License(NDSML 1.0)
 */
class NeoTheme_Blog_Block_Adminhtml_Post_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {

         parent::__construct();

         $this->_objectId = 'id';
         $this->_blockGroup = 'neotheme_blog';
         $this->_controller = 'adminhtml_post';
         $this->_updateButton('save', 'label', Mage::helper('neotheme_blog')->__('Save Post'));
	 $this->_updateButton('delete', 'label', Mage::helper('neotheme_blog')->__('Delete Post'));
         $this->_addButton('save_and_continue', array(
                 'label' => Mage::helper('neotheme_blog')->__('Save And Continue Edit'),
                 'onclick' => 'saveAndContinueEdit()',
                 'class' => 'save' 
             ), -100);
             $this->_formScripts[] = "
                 function saveAndContinueEdit(){
                    editForm.submit($('edit_form').action + 'back/edit/');
                 }
                 ";
        $this->setId('neotheme_blog_edit');

     }
     public function getHeaderText()
     {
         if( Mage::registry('current_post') && Mage::registry('current_post')->getId() ) {
             return Mage::helper('neotheme_blog')->__("Edit Post '%s'", $this->htmlEscape(Mage::registry('current_post')->getTitle()));
         } else {
             return Mage::helper('neotheme_blog')->__('Add Post');
         }
     }
}