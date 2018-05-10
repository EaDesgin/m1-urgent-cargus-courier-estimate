<?php

class Eadesigndev_Urgent_Model_Carrier_Urgent extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'urgentcurrier';

    public function isTrackingAvailable()
    {
        return true;
    }

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {


        if (!Mage::getStoreConfig('carriers/' . $this->_code . '/active')) {
            return false;
        }

        $lei = Mage::helper('urgent')->isLei($request->getData('package_currency'));

        if (!$lei) {
            return false;
        }

        $helper = Mage::helper('urgent');

        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual()) {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }

        $valuare = $request->getPackageValue() * $lei;

        if ($helper->getAfisare() == 2) {

            $methodUrgent = $this->_calculateTotal($request, $valuare, true);
            $result = Mage::getModel('shipping/rate_result');
            $result->append($methodUrgent);

            return $result;

        } else {

            $methodUrgent = $this->_calculateTotal($request, $valuare, false);
            $result = Mage::getModel('shipping/rate_result');
            $result->append($methodUrgent);

            return $result;
        }

    }

    private function _calculateTotal($request, $valuare, $afisare)
    {

        $helper = Mage::helper('urgent');

        $regionid = $helper->getRegionIdByCode($request->getDestRegionCode(), 'RO');

        $list = $helper->getCities('RO', $regionid);

        $city = $request->getDestCity();
        $match = $helper->getClosest($city, $list);

        if (!$list) {
            $match = $city;
        }

        $lei = Mage::helper('urgent')->isLei($request->getData('package_currency'));

        $declaredValue = 0;
        if ($helper->getAsigurare()) {
            $declaredValue = $valuare;
        }

        $cashRepayment = $valuare;
        $bankRepayment = 0;

        if ($afisare) {
            $cashRepayment = 0;
            $bankRepayment = $valuare;
        }

        $parcel = 0;
        $envelopes = 1;
        if ($helper->getParcelsEnvelopes()) {
            $parcel = 1;
            $envelopes = 0;
        }

        $fields = array(
            'Token' => Mage::helper('urgent/urgent')->getToken(true),
            'FromLocalityId' => $helper->getFromCity(),
            'FromCountyName' => '',
            'FromLocalityName' => '',
            'ToLocalityId' => null,
            'ToCountyName' => $request->getDestRegionCode(),
            'ToLocalityName' => $match,
            'Parcels' => $parcel,
            'Envelopes' => $envelopes,
            'TotalWeight' => round($request->getPackageWeight()),
            'DeclaredValue' => $declaredValue,
            'CashRepayment' => $cashRepayment,
            'BankRepayment' => $bankRepayment,
            'OtherRepayment' => '',
            'OpenPackage' => ($helper->getDeschide()),
            'PriceTableId' => $helper->getPlanTarifar()
        );

        $calculate = Mage::helper('urgent/urgent')->getUrgent('CalculateShipping', $fields);

        if (is_array($calculate)) {
            if (isset($calculate['error'])) {
                return false;
            }
        }

        $total = round(($calculate->GrandTotal / $lei), 2);

        $methodUrgent = Mage::getModel('shipping/rate_result_method');
        $methodUrgent->setCarrier($this->_code);
        $methodUrgent->setCarrierTitle($helper->getTitle());
        $methodUrgent->setMethod('urgent');
        $methodUrgent->setMethodTitle($helper->getName());

        $methodUrgent->setPrice($total);
        $methodUrgent->setCost($total);
//        echo '<pre>';
//        print_r($methodUrgent);
//        exit();
        return $methodUrgent;
    }

    public function getAllowedMethods()
    {

    }
}
