<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Collection
 *
 * @author Ea Design
 */
class Eadesigndev_Urgent_Model_Mysql4_Urgent_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Retrive the product collection.
     */
    public function _construct()
    {
        $this->_init('urgent/urgent');
    }
}
