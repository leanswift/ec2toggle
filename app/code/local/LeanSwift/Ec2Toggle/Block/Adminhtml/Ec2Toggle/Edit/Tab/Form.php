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
 class LeanSwift_Ec2Toggle_Block_Adminhtml_Ec2Toggle_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
 {
        protected function _prepareForm()
        {
            $form = new Varien_Data_Form();
            $this->setForm($form);
            $fieldset = $form->addFieldset('ec2Toggle_form', array('legend'=>Mage::helper('ec2Toggle')->__('Server Information')));

            $fieldset->addField('instance_id', 'text', array(
                'label' => Mage::helper('ec2Toggle')->__('Instance ID'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'instance_id',
            ));

            $fieldset->addField('server_name', 'text', array(
                'label' => Mage::helper('ec2Toggle')->__('Server Name'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'server_name',
            ));

            $fieldset->addField('region', 'text', array(
                'label' => Mage::helper('ec2Toggle')->__('Region'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'region',
            ));

            /*$fieldset->addField('current_status', 'text', array(
                'label' => Mage::helper('ec2Toggle')->__('Current Status'),
                'name' => 'current_status',
            ));*/

            $fieldset->addField('status', 'select', array(
                'label'     => Mage::helper('ec2Toggle')->__('Status'),
                'name'      => 'status',
                'values'    => array(
                    array(
                        'value'     => 1,
                        'label'     => Mage::helper('ec2Toggle')->__('Turn On'),
                    ),

                    array(
                        'value'     => 0,
                        'label'     => Mage::helper('ec2Toggle')->__('Turn Off'),
                    ),
                ),
            ));



            if(Mage::getSingleton('adminhtml/session')->getEc2ToggleData())
            {
                $form->setValues(Mage::getSingleton('adminhtmml/session')->getEc2ToggleData());
                Mage::getSingleton('adminhtml/session')->setEc2ToggleData(null);
            }
            elseif(Mage::registry('ec2Toggle_data'))
            {
                $form->setValues(Mage::registry('ec2Toggle_data')->getData());
            }

            return parent::_prepareForm();
        }
 }