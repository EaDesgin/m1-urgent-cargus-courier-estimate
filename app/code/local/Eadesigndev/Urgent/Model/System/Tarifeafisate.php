<?php

class Eadesigndev_Urgent_Model_System_Tarifeafisate extends Eadesigndev_Urgent_Model_System_Abstract
{
    public function toOptionArray()
    {
        return array(
            array('value' => '', 'label' => Mage::helper('urgent')->__('Select')),
            array('value' => '1', 'label' => Mage::helper('urgent')->__('Ramburs')),
            array('value' => '2', 'label' => Mage::helper('urgent')->__('Cont colector')),
        );
    }
}