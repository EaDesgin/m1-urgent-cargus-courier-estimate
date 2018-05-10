<?php

abstract class Eadesigndev_Urgent_Model_System_Abstract
{
    public $helper;

    public function __construct()
    {
        $this->helper = Mage::helper('urgent/urgent');
    }

    public function getHelper()
    {
        return $this->helper;
    }

    public function getError(){
        return array(
            array('value' => '', 'label' => Mage::helper('urgent')->__('Trebuie sa ai un user si parola valide'))
        );
    }

}
