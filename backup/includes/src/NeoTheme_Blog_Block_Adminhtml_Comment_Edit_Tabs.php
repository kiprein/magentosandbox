<?php
/**
 * NeoTheme Austrlia (Neo Industries Pty Ltd)
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
class NeoTheme_Blog_Block_Adminhtml_Comment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
   public function __construct()
   {
        parent::__construct();
        $this->setId('neotheme_blog_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('<img src="' . $this->getSkinUrl('images/neotheme/blog/blog-logo.gif') . '"/><br />'.Mage::helper('neotheme_blog')->__('Post'));
        $this->setHeader(Mage::helper('neotheme_blog')->__('Post'));
   }


   protected function _beforeToHtml()
   {
        $post = $this->getPost();

        $this->addTab('form_section', array(
        			  'label'     => Mage::helper('neotheme_blog')->__('Post Configuration'),
        			  'title'     => Mage::helper('neotheme_blog')->__('Post Configuration'),
         			  'content'   => $this->getLayout()->createBlock('neotheme_blog/adminhtml_comment_edit_tab_form')
                                                ->toHtml(),
        ));
        /*
        $this->addTab('publishing_section', array(
        			  'label'     => Mage::helper('neotheme_blog')->__('Publishing Configuration'),
        			  'title'     => Mage::helper('neotheme_blog')->__('Publishing Configuration'),
         			  'content'   => $this->getLayout()->createBlock('neotheme_blog/adminhtml_post_edit_tab_publishing')
                                                ->toHtml(),
        ));
        $this->addTab('meta_section', array(
        			  'label'     => Mage::helper('neotheme_blog')->__('Metadata'),
        			  'title'     => Mage::helper('neotheme_blog')->__('Metadata'),
         			  'content'   => $this->getLayout()->createBlock('neotheme_blog/adminhtml_post_edit_tab_meta')
                                                ->toHtml(),
        ));*/
         //   $this->addTab('images', array(
       // 	'label'     => Mage::helper('neotheme_blog')->__('Blog/s Assignment'),
       // 	'title'     => Mage::helper('neotheme_blog')->__('Banner/s Assignment'),
        //        'url'       => $this->getUrl('*/*/blog', array('_current' => true)),
         //       'class'     => 'ajax',
          //  ));

        /*$this->addTab('images_form_section', array(
        			  'label'     => Mage::helper('imagerotator')->__('Banner/s Assignment'),
        			  'title'     => Mage::helper('imagerotator')->__('Banner/s Assignment'),
         			  //'content'   => $this->getLayout()->createBlock('imagerotator/adminhtml_image_grid')
                                   'content'   => $this->getLayout()->createBlock('imagerotator/adminhtml_imagerotator_edit_tab_images')
                                                                                                   ->setUseMassaction(true)
         			  								   ->setRotatorId($this->getId())
         			  								   ->toHtml(),
         			  
        ));*/
        
        //set registry

       return parent::_beforeToHtml();
   }
     public function getPost()
    {
      //  if (!($this->getData('current_post') instanceof NeoTheme_Blog_Model_ImageRotator)) {
      //      $this->setData('image_rotator', Mage::registry('image_rotator'));
      //  }
        return $this->getData('current_post');
    }

} 