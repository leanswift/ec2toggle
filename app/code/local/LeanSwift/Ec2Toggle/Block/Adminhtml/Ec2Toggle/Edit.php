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
 class LeanSwift_Ec2Toggle_Block_Adminhtml_Ec2Toggle_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
 {
     public function __construct()
     {
         parent::__construct();
         $this->_objectId = 'id';
         $this->_blockGroup = 'ec2Toggle';
         $this->_controller = 'adminhtml_ec2Toggle';
         $this->_updateButton('save','label',Mage::helper('ec2Toggle')->__('Save Server Details'));
         $this->_updateButton('delete','label',Mage::helper('ec2Toggle')->__('Delete Server Details'));
     }

     public function getHeaderText()
     {
         if(Mage::registry('ec2Toggle_data') && Mage::registry('ec2Toggle_data')->getId())
         {
             return Mage::helper('ec2Toggle')->__("Edit Server Details '%s' ", $this->htmlEscape(Mage::registry('ec2Toggle_data')->getId()));
         }
         else
         {
             return Mage::helper('ec2Toggle')->__('Add Server Details');
         }
     }
 }