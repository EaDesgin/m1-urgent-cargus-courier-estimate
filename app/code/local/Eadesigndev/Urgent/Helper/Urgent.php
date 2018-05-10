<?php
class Eadesigndev_Urgent_Helper_Urgent extends Mage_Core_Helper_Abstract
{
    private $ServiceUrl;
    private $Curl;

    public function __construct()
    {
        $this->ServiceUrl = 'http://urgentcargus.cloudapp.net/IntegrationService.asmx';
        $this->Curl = curl_init();
        curl_setopt($this->Curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($this->Curl, CURLOPT_RETURNTRANSFER, true);
    }

    private function getUrgentData($MethodName, $Parameters = null)
    {
        if ($Parameters != null) {
            $Parameters = json_encode($Parameters);
            curl_setopt($this->Curl, CURLOPT_POSTFIELDS, $Parameters);
        }
        curl_setopt($this->Curl, CURLOPT_URL, $this->ServiceUrl . '/' . $MethodName);
        curl_setopt($this->Curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($Parameters)));
        $data = json_decode(curl_exec($this->Curl));
        if (isset($data->d)) {
            if (strstr($MethodName, 'Print')) {
                return implode(array_map('chr', $data->d));
            } else {
                return $data->d;
            }
        } else {
            return array('error' => isset($data->Message) ? $data->Message : 'unknown');
        }
    }

    public function getToken($simple = false)
    {
        $credentials = array(
            'Username' => Mage::getStoreConfig('carriers/eadesignurgent/username'),
            'Password' => Mage::getStoreConfig('carriers/eadesignurgent/password')
        );

        $token = $this->getUrgentData('LoginUser', $credentials);

        //exit(print_r($credentials));

        if (is_array($token)) {
            if (isset($token['error'])) {
                Mage::getSingleton('core/session')->addError($token['error']);
                return;
            }
        }

        if ($simple) {
            $fields = $token;
        } else {
            $fields = array(
                'Token' => $token
            );
        }

        return $fields;

    }

    public function getPunctRidicare()
    {
        if ($this->getToken()) {
            return $this->getUrgentData('GetPickupLocations', $this->getToken());
        }
        return false;
    }

    public function getPunctToOptonArray()
    {
        if ($this->getToken()) {
            return $this->toOptionArray($this->getPunctRidicare(), 'LocationId', 'Name');
        }
        return false;
    }

    public function toOptionArray($array, $key, $value)
    {
        $return = array();

        if($array['error']){
            return $return;
        }

        foreach ($array as $element) {
            $return[$element->$key] = $element->$value;
        }
        return $return;
    }

    public function getPricePlans()
    {
        $planTarifar = $this->getUrgentData('GetPriceTables', $this->getToken());
        return $this->toOptionArray($planTarifar, 'PriceTableId', 'Name');
    }

    public function getUrgent($method, $params = null)
    {
        return $this->getUrgentData($method,$params);
    }
}
