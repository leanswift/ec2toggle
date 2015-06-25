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
 class LeanSwift_Ec2Toggle_Block_Adminhtml_Ec2Toggle_Grid extends Mage_Adminhtml_Block_Widget_Grid
 {
    public function __construct()
    {
        parent::__construct();
        $this->setId('ec2ToggleGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ec2Toggle/ec2Toggle')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        /*$this->addColumn('id', array(
            'header' => Mage::helper('ec2Toggle')->__('ID'),
            'align'  => 'center',
            'width'  => '5px',
            'index'  => 'id',
        ));*/

        $this->addColumn('instance_id', array(
           'header' => Mage::helper('ec2Toggle')->__('Instance ID'),
           'align'  => 'center',
           'width'  => '50px',
           'index'  => 'instance_id',
       ));

        $this->addColumn('server_name', array(
            'header' => Mage::helper('ec2Toggle')->__('Server Name'),
            'align'  => 'center',
            'width'  => '50px',
            'index'  => 'server_name',
        ));

        $this->addColumn('region', array(
            'header' => Mage::helper('ec2Toggle')->__('Region'),
            'align'  => 'center',
            'width'  => '50px',
            'index'  => 'region',
        ));

        $this->addColumn('current_status', array(
            'header' => Mage::helper('ec2Toggle')->__('Current Status'),
            'align'  => 'center',
            'width'  => '50px',
            'index'  => 'current_status',
        ));

        $this->addColumn('update_time', array(
            'header'    => Mage::helper('ec2Toggle')->__('Last Update'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'datetime',
            'index'     => 'update_time',
        ));

       return parent::_prepareColumns();
    }

     protected function _prepareMassaction()
     {
         $this->setMassactionIdField('id');
         $this->getMassactionBlock()->setFormFieldName('check_id');

//         $this->getMassactionBlock()->addItem(
//             'delete',
//             array(
//                 'label'   => Mage::helper('ec2Toggle')->__('Delete'),
//                 'url'     => $this->getUrl('*/*/massDelete'),
//                 'confirm' => Mage::helper('ec2Toggle')->__('Are you sure?'),
//             )
//         );

         $statuses = Mage::getModel('ec2Toggle/ec2Toggle')->getOptionArray();

         //array_unshift($statuses, array('label' => '', 'value' => ''));
         $this->getMassactionBlock()->addItem(
             'change_status',
             array(
                 'label'      => Mage::helper('ec2Toggle')->__('Change status'),
                 'url'        => $this->getUrl('*/*/massStatus', array('_current' => true)),
                 'additional' => array(
                     'visibility' => array(
                         'name'   => 'change_status',
                         'type'   => 'select',
                         'class'  => 'required-entry',
                         'label'  => Mage::helper('ec2Toggle')->__('Change Status'),
                         'values' => $statuses,
                     )
                 )
             )
         );
         return $this;
     }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
 }