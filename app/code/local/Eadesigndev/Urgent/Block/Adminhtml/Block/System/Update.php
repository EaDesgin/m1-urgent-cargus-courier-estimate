<?php

class Eadesigndev_Urgent_Block_Adminhtml_Block_System_Update extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = Mage::helper('adminhtml')->getUrl('urgentadmin/adminhtml_index/update');

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('scalable')
            ->setLabel('Update lista orase!')
            ->setOnClick("setLocation('$url')")
            ->toHtml();

        return $html;
    }
}