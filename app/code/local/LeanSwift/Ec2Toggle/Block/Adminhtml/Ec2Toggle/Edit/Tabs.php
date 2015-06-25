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
 class LeanSwift_Ec2Toggle_Block_Adminhtml_Ec2Toggle_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
 {
     public function __construct()
     {
         parent::__construct();
         $this->setId('ec2Toggle_tabs');
         $this->setDestElementId('edit_form');
         $this->setTitle(Mage::helper('ec2Toggle')->__('Server Information'));
     }

     protected function _beforeToHtml()
     {
         $this->addTab('form_section',array(
             'label' => Mage::helper('ec2Toggle')->__('Server Information'),
             'title' => Mage::helper('ec2Toggle')->__('Server Information'),
             'content' => $this->getLayout()->createBlock('ec2Toggle/adminhtml_ec2Toggle_edit_tab_form')->toHtml(),
         ));

         return parent::_beforeToHtml();
     }
 }