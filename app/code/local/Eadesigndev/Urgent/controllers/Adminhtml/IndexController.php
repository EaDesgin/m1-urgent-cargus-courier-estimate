<?php

/**
 *
 * @author Ea Design
 */
class  Eadesigndev_Urgent_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function updateAction()
    {
        Mage::helper('urgent/update')->getUrgentCollection();
        $this->_redirectReferer();
    }
}
