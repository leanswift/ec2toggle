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
class LeanSwift_Ec2Toggle_Block_Adminhtml_Ec2Toggle_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
 {
     protected function _prepareForm()
     {
         $form = new Varien_Data_Form(array(
             'id'=> 'edit_form',
             'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
             'method' => 'post',
         ));

         $form->setUseContainer(true);
         $this->setForm($form);
         return parent::_prepareForm();
     }
 }