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
use Aws\Ec2\Ec2Client;
use Aws\Common\Enum\Region;
use Aws\Ec2\Enum\InstanceType;
use Aws\Ec2\Enum\InstanceStateName;
/* Linux Server - Use the require one below code */
require_once('aws-autoloader-linux.php');
/* Windows Server - Use the require one below code */
require_once('aws-autoloader-windows.php');
class LeanSwift_Ec2Toggle_Model_Ec2Toggle extends Mage_Core_Model_Abstract
 {
     public function _construct()
     {
         parent::_construct();
         $this->_init('ec2Toggle/ec2Toggle');
     }

     public function getOptionArray()
     {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('Turn On')),
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('Turn Off')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('Check Status')),
        );
     }

     /*Server state initiate and Check current state */
     public function doRequest($postData)
     {
         $cmd = $postData['status'];
         if($cmd == 1)
         {
             $cmd = 'start';
         }
         elseif($cmd == 0)
         {
             $cmd = 'stop';
         }
         else
         {
             $cmd = 'check';
         }
         try{
             $client = Ec2Client::factory(array(
                 'key' => Mage::helper('core')->decrypt(Mage::getStoreConfig('ec2Toggle/aws_credentials/aws_key')), // your auth API key
                 'secret' => Mage::helper('core')->decrypt(Mage::getStoreConfig('ec2Toggle/aws_credentials/aws_secret')), // your secret API key
                 'region' => $postData['region'],
             ));
             // Start Instances
             if ($cmd == 'start') {
                 $result = $client->startInstances(array(
                     'InstanceIds' => array($postData['instance_id'],),
                     'DryRun' => false,
                 ));
                 $client->waitUntilInstanceRunning(array('InstanceIds' => array($postData['instance_id'],)));
                 $resultArray = $result['StartingInstances'];
                 foreach($resultArray as $instance)
                 {
                     $instanceArray = $instance['CurrentState'];
                     foreach($instanceArray as $key => $currentState)
                     {
                         if($key=="Name")
                         {
                             $postData['current_state'] = ucfirst($currentState);
                             //Mage::log("Current Status: ".$postData['current_state']);
                         }
                     }
                 }
             }
             // Stop Instances
             elseif ($cmd == 'stop') {
                 $result = $client->stopInstances(array(
                     'InstanceIds' => array($postData['instance_id'],),
                     'DryRun' => false,
                 ));
                 $client->waitUntilInstanceStopped(array('InstanceIds' => array($postData['instance_id'],)));
                 $resultArray = $result['StoppingInstances'];
                 foreach($resultArray as $instance)
                 {
                     $instanceArray = $instance['CurrentState'];
                     foreach($instanceArray as $key => $currentState)
                     {
                         if($key=="Name")
                         {
                             $postData['current_state'] = ucfirst($currentState);
                             //Mage::log("Current Status: ".$postData['current_state']);
                         }
                     }
                 }
             }
             // Check Instances
             $result = $client->describeInstances(array('InstanceIds' => array($postData['instance_id'])));
             $reservationArray = $result['Reservations'];
             foreach($reservationArray as $reservation)
             {
                 $instancesArray = $reservation['Instances'];
                 foreach($instancesArray as $instance)
                 {
                     $statusArray = $instance['State'];
                     foreach($statusArray as $key => $currentState)
                     {
                         if($key == "Name")
                         {
                             $postData['current_state'] = ucfirst($currentState);
                             //Mage::log("Current Status: ".$postData['current_state']);
                         }
                     }
                 }
             }

             return array('current_state'=>$postData['current_state'],'cmd'=>$cmd);

         }catch (Exception $e)
         {
             Mage::log($e->getMessage());
             Mage::getSingleton('adminhtml/session')->addError($postData['instance_id'].': [AWS request failed] '.$e->getMessage());
         }
     }
 }