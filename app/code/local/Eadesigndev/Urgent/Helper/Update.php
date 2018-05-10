<?php
class Eadesigndev_Urgent_Helper_Update extends Mage_Core_Helper_Abstract
{
    public function getDataCity()
    {
        $token = Mage::helper('urgent/urgent')->getToken(true);

        $fields = array(
            'Token' => $token,
            'CountyId' => null,
            'CountyName' => '',
            'CountryId' => null,
            'CountryName' => 'Romania'
        );

        $localities = Mage::helper('urgent/urgent')->getUrgent('GetLocalities', $fields);

        return $localities;
    }

    public function getDataCounty()
    {
        $token = Mage::helper('urgent/urgent')->getToken(true);

        $fields = array(
            'Token' => $token,
            'CountryId' => null,
            'CountryName' => 'Romania'
        );

        return Mage::helper('urgent/urgent')->getUrgent('GetCounties', $fields);
    }


    public function getCity()
    {
        $cities = $this->getDataCity();

        return $cities;
    }

    public function getCounty()
    {
        $counties = $this->getDataCounty();

        return $counties;
    }

    public function processData()
    {
        $collection = Mage::getModel('directory/region')->getResourceCollection()
            ->addCountryFilter('RO')
            ->load();

        $regionNew = array();
        foreach ($collection as $region) {
            $regionNew[$region->getData('code')] = $region->getData('region_id');
        }

        $orase = $this->getCity();
        $judete = $this->getCounty();

        $listaJudeteNoua = array();

        foreach ($judete as $judet) {
            $listaJudeteNoua[$judet->CountyId] = $judet->Abbreviation;
        }

        $listaOraseNoua = array();

        foreach ($orase as $oras) {
            $judetNumeric = $oras->CountyId;
            $judetCod = $listaJudeteNoua[$judetNumeric];
            $judetId = $regionNew[$judetCod];

            $listaOraseNoua[] = array(
                'RO',
                $oras->Name,
                $judetId
            );
        }

        return $listaOraseNoua;

    }

    public function getUrgentCollection()
    {
        $collection = Mage::getModel('urgent/urgent')->getResourceCollection()
            ->load();

        foreach ($collection as $individualObject) {
            $individualObject->delete();
        }

        foreach ($this->processData() as $oras) {
            $individualOras = Mage::getModel('urgent/urgent');
            $individualOras->setData('country_id', $oras[0]);
            $individualOras->setData('region_id', $oras[2]);
            $individualOras->setData('cityname', $oras[1]);
            try {
                $individualOras->save();
            } catch (Exception $e) {
                $messege = $e->getMessege();
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('urgent')->__('A avut loc o eroare ' . $messege));
            }
        }

        $individualuc = Mage::getModel('urgent/urgent')
            ->setData('country_id', 'RO')
            ->setData('region_id', '287')
            ->setData('cityname', 'Bucuresti');

        try {
            $individualuc->save();
        } catch (Exception $e) {
            $messege1 = $e->getMessege();
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('urgent')->__('A avut loc o eroare ' . $messege1));
        }

        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('urgent')->__('A fost facut update-ul'));
    }
}
