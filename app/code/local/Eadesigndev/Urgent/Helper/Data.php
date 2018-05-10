<?php

class Eadesigndev_Urgent_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isLei($object)
    {

        $code = $object->getData('currency_code');
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();

        if ($baseCurrencyCode == 'RON') {
            return 1;
        }

        if ($code == 'RON') {
            $allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
            $rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode, array_values($allowedCurrencies));

            return $rates['RON'];
        }

        return false;
    }

    public function getRegionIdByCode($regionCode, $countryCode)
    {
        $regionModel = Mage::getModel('directory/region')->loadByCode($regionCode, $countryCode);
        $regionId = $regionModel->getId();

        return $regionId;
    }

    public function getCities($countryId, $regionId)
    {
        $cityCollection = Mage::getModel('urgent/urgent')->getCollection();
        $cityCollection->addFieldToSelect('cityname')
            ->addFieldToFilter('country_id', $countryId)
            ->addFieldToFilter('region_id', $regionId);

        $count = count($cityCollection->getData());

        if($count < 2){
            return false;
        }

        foreach ($cityCollection as $citiesData) {
            $cites[] = $citiesData->getData('cityname');
        }

        return $cites;
    }

    public function getFromCity()
    {
        return Mage::getStoreConfig('carriers/eadesignurgent/punctridicare');
    }

    public function getParcelsEnvelopes()
    {
        return Mage::getStoreConfig('carriers/eadesignurgent/pachet');
    }

    public function getPlanTarifar()
    {
        return Mage::getStoreConfig('carriers/eadesignurgent/plantarifar');
    }

    public function getDeschide()
    {
        if (Mage::getStoreConfig('carriers/eadesignurgent/deschide')) {
            return true;
        }
        return false;
    }

    public function getAsigurare()
    {
        return Mage::getStoreConfig('carriers/eadesignurgent/asigurare');
    }

    public function getAfisare()
    {
        return Mage::getStoreConfig('carriers/eadesignurgent/afisare');
    }

    public function getTitle()
    {
        return Mage::getStoreConfig('carriers/eadesignurgent/title');
    }

    public function getName()
    {
        return Mage::getStoreConfig('carriers/eadesignurgent/name');
    }

    public function getClosest($input, $words)
    {

        $shortest = -1;

        foreach ($words as $word) {

            $lev = levenshtein($input, $word);

            if ($lev == 0) {

                $closest = $word;
                $shortest = 0;

                break;
            }

            if ($lev <= $shortest || $shortest < 0) {
                $closest = $word;
                $shortest = $lev;
            }
        }
        return $closest;
    }

}
