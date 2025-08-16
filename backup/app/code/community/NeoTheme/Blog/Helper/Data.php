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
class NeoTheme_Blog_Helper_Data extends Mage_Core_Helper_Abstract {

    const XPATH_CONFIG_DRAFT_IPS = 'blog/general/draft_ips';
    const XPATH_CONFIG_ADD_IPS_ON_SAVE = 'blog/general/add_ips_on_save';
    const XPATH_CONFIG_CUSTOMER_PREFERENCES_ENABLE = 'blog/customer/enabled';
    const XPATH_CONFIG_RSS_ACTIVE = 'rss/neotheme_blog/active';
    const XPATH_COMMENTS_COUNT = 'blog/comment/comments_count';
    const XPATH_CONFIG_DATETIME_FORMAT_TYPE = 'blog/general/time_format';
    const XPATH_CONFIG_DATETIME_CUSTOM_FORMAT = 'blog/general/time_format_custom';
    const XPATH_COMMENT_TEMPLATE_DEFAULT = 'neotheme/blog/comment/form/default.phtml';
    const XPATH_COMMENT_TEMPLATE = 'blog/comment/type_template';
    const XPATH_FRONTEND_URL_KEY = 'blog/general/frontend_url_key';
    const XPATH_FACEBOOK_COMMENT_WIDTH = 'blog/comment/facebook_comment_width';
    const XPATH_CATEGORY_SHOW_POPULATED = 'blog/category/show_populated_only';
    const XPATH_CUSTOMER_GROUP_FILTERING = 'blog/general/customer_group_filtering';

    const XPATH_DEFAULT_META_KEYWORDS = 'blog/frontend/default_meta_keywords';
    const XPATH_DEFAULT_META_DESCRIPTION = 'blog/frontend/default_meta_description';

	const FRONTEND_NAME = 'blog';

    static function isIpPermitted($ip = NULL, $store_id = NULL) {
        if ($ip == NULL) {
            $ip = Mage::helper('core/http')->getRemoteAddr();
        }
        //if ($store_id == NULL){
        //    $store_id = Mage::app()->getStore()->getId();
        // }
        return (strpos(Mage::getStoreConfig(self::XPATH_CONFIG_DRAFT_IPS, $store_id), $ip) !== false) ? true : false;
    }

    static function addIpToPermittedIps($ip = null) {
        if ($ip == NULL) {
            $ip = Mage::helper('core/http')->getRemoteAddr();
        }
        if (Mage::getStoreConfig(self::XPATH_CONFIG_ADD_IPS_ON_SAVE) && !self::isIpPermitted($ip)) {
            $current_ips = explode(",", Mage::getStoreConfig(self::XPATH_CONFIG_DRAFT_IPS));
            $current_ips[] = $ip;
            Mage::getConfig()->saveConfig(self::XPATH_CONFIG_DRAFT_IPS, implode(",", $current_ips));
            return true;
        }
        return false;
    }

	static function getCategoryOptionValues($inlcudeNone = false) {
        $collection = Mage::getModel('neotheme_blog/category')->getCollection();
        $values = array();
		if ($inlcudeNone){
			$values[] = array('label' => "--None--", 'value' => 0);
		}
        foreach ($collection as $category) {
			$values[] = array('label' => $category->getName(), 'value' => $category->getId());
        }
        return $values;
    }

    static function getRssUrl() {
        return Mage::getSingleton('core/url')->getUrl('rss/blog/index');
    }

    static function isRSSActive() {
        return Mage::getStoreConfig(self::XPATH_CONFIG_RSS_ACTIVE);
    }

    static function isDefaultCommentingEnabled($storeId = null) {
        return (Mage::getStoreConfig(self::XPATH_COMMENT_TEMPLATE, $storeId) == self::XPATH_COMMENT_TEMPLATE_DEFAULT);
    }

    protected $_frontendName;

    static public function getFrontendName($ucFirst = false) {
        $frontendName = Mage::getStoreConfig('blog/general/frontend_url_key');
        if (!$frontendName) {
            $frontendName = self::FRONTEND_NAME;
        }
        if ($ucFirst) {
            $frontendName = ucfirst($frontendName);
        }
        return $frontendName;
    }



    static public function getBlogLabel() {
        return self::getFrontendName(true);
    }

    static public function getBlogUrl() {
        return Mage::getSingleton('core/url')->getUrl(self::getFrontendName());
    }

    static public function getBlogTitle() {
        return self::getFrontendName(true);
    }

    static public function getFacebookWidgetWidth($storeId = null) {
        $width = Mage::getStoreConfig(NeoTheme_Blog_Helper_Data::XPATH_FACEBOOK_COMMENT_WIDTH, $storeId);
        if (!$width) {
            $width = 435;
        }
        return $width;
    }

}