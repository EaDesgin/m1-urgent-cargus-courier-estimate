<?php

class Eadesigndev_Urgent_Model_System_Pachet extends Eadesigndev_Urgent_Model_System_Abstract
{
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('urgent')->__('Colet')),
            array('value' => '1', 'label' => Mage::helper('urgent')->__('Plic')),
        );
    }
}