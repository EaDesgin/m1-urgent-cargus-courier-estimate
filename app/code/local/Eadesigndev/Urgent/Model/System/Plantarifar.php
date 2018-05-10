<?php

class Eadesigndev_Urgent_Model_System_Plantarifar extends Eadesigndev_Urgent_Model_System_Abstract
{
    public function toOptionArray()
    {
        $helper = $this->getHelper();
        $plans = $helper->getPricePlans();

        if(empty($plans)){
            return $this->getError();
        }

        return $plans;
    }
}