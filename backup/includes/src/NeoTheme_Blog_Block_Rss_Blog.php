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
class NeoTheme_Blog_Block_Rss_Blog
extends Mage_Core_Block_Template 
implements Mage_Widget_Block_Interface
{	

    function getPost(){
        if (Mage::registry('current_post') == NULL){
            if ($this->getPostId() != NULL){
                Mage::register('current_post', Mage::getModel('neotheme_blog/post')->load($this->getPostId()));
            }
        }
        return Mage::registry('current_post');
    }
    
    private $_categoryIds;
    private $_categoryCollection;
    function getCategoryCollection(){
        if($this->_categoryCollection == NULL){
            if ($this->_categoryIds == NULL){
                $this->_categoryIds = array();
                foreach($this->getPostCollection() as $post){
                    $catgoryIds = $post->getCategoryIds();
                    foreach($catgoryIds as $categoryId){
                        $this->_categoryIds[] = $categoryId;
                    }
                } 
                $this->_categoryIds = array_keys(array_flip($this->_categoryIds));
            }
            $this->_categoryCollection =   Mage::getModel('neotheme_blog/category')->getCollection()
                                                                    ->addFieldToFilter('entity_id', $this->_categoryIds);
        }
        return $this->_categoryCollection;
    }
    
    
    
    function getCategoryNames($categoryIds){
        if ($this->getCategoryFilterId()){
           //limit the names to the selected category
            $categoryIds = array($this->getCategoryFilterId());
        }
        
        $categoryNames = array();
        if ($categoryIds){
            foreach($this->getCategoryCollection() as $category){
                if (in_array((int)$category->getId(), $categoryIds)){
                    $categoryNames[] = array('term' => $category->getName());
                }
            }
        }
        return $categoryNames;
    }
    

    public function addNewPostXmlCallback($args)
    {

        $rssObj = $args['rssObj'];
        $post = $args['post'];
        $detailBlock = $args['detailBlock'];

        $post->reset()->load( $args['row']['entity_id']);

        if ($post && $post->getId()) {
            $title = Mage::helper('rss')->__($post->getTitle());

            $url = $post->getReadMoreUrl();
            $detailBlock->setPost($post);
            $data = array(
                    'title'         => $title,
                    'link'          => $url,
                    'description'   => $detailBlock->toHtml(),
                    'lastUpdate'       => strtotime($post->getCreatedAt()),
                    'category'      => $this->getCategoryNames($post->getCategoryIds()),
                    
                    );

            $rssObj->_addEntry($data);
       }

    }
    
    private $_collection;
    protected function getPostCollection(){
        if ($this->_collection == NULL){
            $this->_collection = $this->getPostModel()->getCollection()
                    ->addStatusFilter(NeoTheme_Blog_Model_Post::STATUS_ACTIVE);
            if ($this->getCategoryFilterId()){
                $this->_collection->addCategoryFilter($this->getCategoryFilterId());
            }
            
            if ($storeId = $this->getStoreId()){
                if (!is_numeric($storeId)){
                    $storeId = Mage::app()->getStore($storeId)->getId();
                }
                $this->_collection
                        ->addStoreFilter($storeId);
            }

        }

        return $this->_collection;
    }
    
    protected function getPostModel(){
        return Mage::getModel('neotheme_blog/post');
    }

    protected function _toHtml()
    {
        $postModel = $this->getPostModel();
        //$passDate = $postModel->getResource()->formatDate(mktime(0,0,0,date('m'),date('d')-7));

        
        $newurl = Mage::getSingleton('core/url')->getUrl('neotheme_blog');
        $title = Mage::helper('rss')->__('Blog Posts');
        $rssObj = Mage::getModel('rss/rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                'generator'   => 'Neotheme Blog Module http://www.neotheme.com/',
                'author'      => Mage::app()->getStore()->getFrontendName(),
                'copyright'   => preg_replace('/[^A-Za-z0-9\s.\s-]/','',Mage::getStoreConfig('design/footer/copyright')) 
                );
        $rssObj->_addHeader($data);

        $collection = $this->getPostCollection();
        
        $detailBlock = Mage::getBlockSingleton('neotheme_blog/rss_post_details');

        Mage::dispatchEvent('rss_blog_new_collection_select', array('collection' => $collection));
        Mage::getSingleton('core/resource_iterator')
            ->walk($collection->getSelect(), array(array($this, 'addNewPostXmlCallback')), array('rssObj'=> $rssObj, 'post'=>$postModel, 'detailBlock' =>  $detailBlock));
        return $rssObj->createRssXml();
    }
}