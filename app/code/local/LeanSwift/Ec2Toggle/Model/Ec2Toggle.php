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
use Aws\Ec2\Ec2Client;
use Aws\Common\Enum\Region;
use Aws\Ec2\Enum\InstanceType;
use Aws\Ec2\Enum\InstanceStateName;
/* Linux Server - Use the require one below code */
require_once('aws-autoloader-linux.php');
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