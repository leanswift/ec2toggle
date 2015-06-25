<?php
/**
 * LeanSwift Ec2Toggle Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the LeanSwift Ec2Toggle Extension License
 * that is bundled with this package in the file LICENSE.txt located in the Connector Server.
 *
 * DISCLAIMER
 *
 * This extension is licensed and distributed by LeanSwift. Do not edit or add to this file
 * if you wish to upgrade Extension and Ec2Toggle to newer versions in the future.
 * If you wish to customize Extension for your needs please contact LeanSwift for more
 * information. You may not reverse engineer, decompile,
 * or disassemble LeanSwift Ec2Toggle Extension (All Versions), except and only to the extent that
 * such activity is expressly permitted by applicable law not withstanding this limitation.
 *
 * @copyright   Copyright (c) Leanswift Solutions, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 * Proprietary and confidential.
 *
 * @category   Ec2Toggle
 * @package    LeanSwift Solutions
 * @author     Janagiram <janagiram@leanswift.com>
 * @version    Ec2Toggle 1.0
 * @license    http://www.leanswift.com/license/ec2Toggle-extension
 */
 class LeanSwift_Ec2Toggle_Adminhtml_Ec2ToggleController extends Mage_Adminhtml_Controller_Action
 {
     protected function _initAction()
     {
         $this->loadLayout()
             ->_setActiveMenu('ec2Toggle/items')
             ->_addBreadcrumb(Mage::helper('adminhtml')->__('Server Manager'),Mage::helper('adminhtml')->__('Server Manager'));
         return $this;
     }

     public function indexAction()
     {
         $this->_initAction();
         $this->_addContent($this->getLayout()->createBlock('ec2Toggle/adminhtml_ec2Toggle'));
         $this->renderLayout();
     }

     public function editAction()
     {
         $serverId = $this->getRequest()->getParam('id');
         $serverModel = Mage::getModel('ec2Toggle/ec2Toggle')->load($serverId);
         if($serverModel->getId()|| $serverId==0)
         {
             Mage::register('ec2Toggle_data',$serverModel);
             $this->loadLayout();
             $this->_setActiveMenu('ec2Toggle/items');
             $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Server Manager'), Mage::helper('adminhtml')->__('Server Manager'));
             $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Ec2Toggle'), Mage::helper('adminhtml')->__('Ec2Toggle'));
             $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
             $this->_addContent($this->getLayout()->createBlock('ec2Toggle/adminhtml_ec2Toggle_edit'))
                 ->_addLeft($this->getLayout()->createBlock('ec2Toggle/adminhtml_ec2Toggle_edit_tabs'));
             $this->renderLayout();
         }
         else
         {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Server Details does not exist'));
             $this->_redirect('*/*/');
         }
     }

     public function newAction()
     {
         $this->_forward('edit');
     }

     public function saveAction()
     {
         if($this->getRequest()->getPost())
         {
             $postData = $this->getRequest()->getPost();
             $postData['id'] = $this->getRequest()->getParam('id');
             $response = Mage::getModel('ec2Toggle/ec2Toggle')->doRequest($postData);
             Mage::log("Current Status: ".$response['current_state']);
             if($response['current_state'])
             {
                 $serverModel = Mage::getModel('ec2Toggle/ec2Toggle');
                 $serverModel->setId($postData['id']);
                 if($response['cmd'] != "check")
                 {
                     $serverModel->setInstanceId($postData['instance_id']);
                     $serverModel->setServerName($postData['server_name']);
                     $serverModel->setRegion($postData['region']);
                     $serverModel->setStatus($postData['status']);
                 }
                 $serverModel->setCurrentStatus($response['current_state']);
                 $serverModel->setUpdateTime(date(time()));
                 $serverModel->save();
                 Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__($postData['server_name'].'('.$postData['instance_id'].') is Saved & '.$response['current_state'].' Successfully'));
                 Mage::getSingleton('adminhtml/session')->setEc2ToggleData(false);
                 $this->_redirect('*/*/');
                 return;
             }
         }
         $this->_redirect('*/*/');
     }

     public function deleteAction()
     {
         if( $this->getRequest()->getParam('id') > 0 ) {
             try {
                 $serverModel = Mage::getModel('ec2Toggle/ec2Toggle');

                 $serverModel->setId($this->getRequest()->getParam('id'))
                     ->delete();

                 Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Server Details were successfully deleted'));
                 $this->_redirect('*/*/');
             } catch (Exception $e) {
                 Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                 $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
             }
         }
         $this->_redirect('*/*/');
     }

     public function gridAction()
     {
         $this->loadLayout();
         $this->getResponse()->setBody($this->getLayout()->createBlock('ec2Toggle/adminhtml_ec2Toggle_grid')->toHtml());
     }

     public function massStatusAction()
     {
         $changeStatus = $this->getRequest()->getParam('change_status');
         $ec2Ids = $this->getRequest()->getParam('check_id');
         if (!is_array($ec2Ids)) {
             Mage::getSingleton('adminhtml/session')->addError($this->__('Please select server(s)'));
         } else {
             try {
                     foreach($ec2Ids as $ids)
                     {
                         $collection = Mage::getModel('ec2Toggle/ec2Toggle')->load($ids);
                         $postData['id'] = $collection->getId();
                         $postData['instance_id'] = $collection->getInstanceId();
                         $postData['server_name'] = $collection->getServerName();
                         $postData['region'] = $collection->getRegion();
                         $postData['status'] = $changeStatus;
                         $response = Mage::getModel('ec2Toggle/ec2Toggle')->doRequest($postData);
                         if($response['current_state'])
                         {
                             $serverModel = Mage::getModel('ec2Toggle/ec2Toggle');
                             $serverModel->setId($postData['id']);
                             if($response['cmd'] != "check")
                             {
                                 $serverModel->setInstanceId($postData['instance_id']);
                                 $serverModel->setServerName($postData['server_name']);
                                 $serverModel->setRegion($postData['region']);
                                 $serverModel->setStatus($postData['status']);
                             }
                             $serverModel->setCurrentStatus($response['current_state']);
                             $serverModel->setUpdateTime(date(time()));
                             $serverModel->save();
                             Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__($postData['server_name'].'('.$postData['instance_id'].') is '.$response['current_state']));
                             Mage::getSingleton('adminhtml/session')->setEc2ToggleData(false);
                             $this->_redirect('*/*/');
                             return;
                         }
                     }
             } catch (Exception $e) {
                 $this->_getSession()->addError($e->getMessage());
             }
         }
         $this->_redirect('*/*/index');
     }
 }