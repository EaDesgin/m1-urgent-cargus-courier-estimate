<?php

class Eadesigndev_Urgent_Model_System_Punctridicare extends Eadesigndev_Urgent_Model_System_Abstract
{
    public function toOptionArray()
    {
        $helper = $this->getHelper();
        $punct = $helper->getPunctToOptonArray();

        if(empty($punct)){
            return $this->getError();
        }

        return $punct;
    }
}