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