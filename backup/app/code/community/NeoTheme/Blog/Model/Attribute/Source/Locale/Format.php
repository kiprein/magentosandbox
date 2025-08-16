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
class NeoTheme_Blog_Model_Attribute_Source_Locale_Format
extends NeoTheme_Blog_Model_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
			array(
			'value'     => Mage_Core_Model_Locale::FORMAT_TYPE_FULL,
			'label'     => Mage::helper('neotheme_blog')->__('Full'),
			),
			array(
			'value'     => Mage_Core_Model_Locale::FORMAT_TYPE_LONG,
			'label'     => Mage::helper('neotheme_blog')->__('Long'),
			),
			array(
			'value'     => Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
			'label'     => Mage::helper('neotheme_blog')->__('Medium'),
			),
			array(
			'value'     => Mage_Core_Model_Locale::FORMAT_TYPE_SHORT,
			'label'     => Mage::helper('neotheme_blog')->__('Short'),
			),
			array(
			'value'     => 'custom',
			'label'     => Mage::helper('neotheme_blog')->__('Custom'),
			),
		);
        }
        return $this->_options;
    }
    
}