<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Cms
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
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

/**
 * Cms Controller Router
 *
 * @category    Mage
 * @package     Mage_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class NeoTheme_Blog_Controller_Router extends Mage_Cms_Controller_Router
{
    const FRONTEND_NAME = 'blog';
    
    /**
     * Initialize Controller Router
     *
     * @param Varien_Event_Observer $observer
     */
    public function initControllerRouters($observer)
    {
        /* @var $front Mage_Core_Controller_Varien_Front */
        $front = $observer->getEvent()->getFront();

        $front->addRouter('neotheme_blog', $this);
    }
    private $_frontendName;
    private function getFrontendName(){
        if (!$this->_frontendName){
            $this->_frontendName = Mage::getStoreConfig('blog/general/frontend_url_key');
            if (!$this->_frontendName){
                $this->_frontendName = self::FRONTEND_NAME;
            }
        }
        return $this->_frontendName;
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }

        $identifier = trim($request->getPathInfo(), '/');
        $idarray = explode('/',$identifier);
        if ($idarray[0] != $this->getFrontendName()){
           return false;
        }
        if (!isset($idarray[1]) || empty($idarray[1])){
            //this could be handled a little better
            $controllerName = 'index';
            $actionName = 'index';
            $params = $request->getParams();
            $request->setModuleName(self::FRONTEND_NAME)
                    ->setControllerName($controllerName)
                    ->setActionName($actionName)
                    ->setParams($params);
            $request->setAlias(
                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,$this->getFrontendName()
            );
            return true;
        }
        unset($idarray[0]);
        $identifier = implode("/",$idarray);
        
        
        
        $condition = new Varien_Object(array(
            'identifier' => $identifier,
            'continue'   => true
        ));
        Mage::dispatchEvent('neotheme_blog_controller_router_match_before', array(
            'router'    => $this,
            'condition' => $condition
        ));
        $identifier = $condition->getIdentifier();

        if ($condition->getRedirectUrl()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return true;
        }

        if (!$condition->getContinue()) {
            return false;
        }
        
        //order here is important, we can to check for category first, then replace the id with the post id if found.
        $modelsToCheck = array('category', 'post');
        
        $matched = false;
        $actionName = false;
        $controllerName = false;
        $params = array();
        foreach($modelsToCheck as $modelName){
            $model = Mage::getModel('neotheme_blog/' . $modelName);
            if ($model instanceof NeoTheme_Blog_Model_IFrontendRoute){
                foreach($idarray as $_identifier){
                    if ($id = $model->checkIdentifier($_identifier,  Mage::app()->getStore()->getId())){
                        $matched = true;
                        $controllerName = $model->getRouteControllerName();
                        $actionName = $model->getRouteActionName();
                        $params = array_merge($params, array_merge($model->getRouteParams($id), $request->getParams()));
    //                    break;
                    }
                }
            }
        }
        if (!$matched){
            return false;
        }
        $request->setModuleName(self::FRONTEND_NAME)
                ->setControllerName($controllerName)
                ->setActionName($actionName)
                ->setParams($params);
                //->setParam('page_id', $pageId);
        $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,$this->getFrontendName() ."/".$identifier
        );
        return true;
    }
}