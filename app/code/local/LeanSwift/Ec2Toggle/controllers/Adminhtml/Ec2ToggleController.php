<?php
/**
 * LeanSwift Ec2Toggle Extension
 *
 * NOTICE OF LICENSE
 *
 * GNU GENERAL PUBLIC LICENSE
 * Version 3, 29 June 2007

 * Copyright (C) 2007 Free Software Foundation, Inc. <http://fsf.org/>
 * Everyone is permitted to copy and distribute verbatim copies
 * of this license document, but changing it is not allowed.
 *
 * @category   Ec2Toggle
 * @package    LeanSwift
 * @author     Janagiram <janagiram@leanswift.com>
 * @version    1.0
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