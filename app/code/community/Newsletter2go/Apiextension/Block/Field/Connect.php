<?php

class Newsletter2go_Apiextension_Block_Field_Connect extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    CONST N2GO_CONNECT_URL = 'https://ui.newsletter2go.com/integrations/connect/MAG/';

    /**
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('newsletter2go/system/config/connect.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Returns URL needed in order to connect to Newsletter2Go
     *
     * @return string URL for connect button
     */
    public function getConnectUrlParams()
    {
        $params = array();
        $params['version'] = '3200';
        $params['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $params['adminUrl'] = Mage::getUrl('adminhtml');

        $params['consumerKey'] = Mage::getStoreConfig('newsletter2go/apiextension/consumer_key');
        $params['consumerSecret'] = Mage::getStoreConfig('newsletter2go/apiextension/consumer_secret');

        // if ID is set get consumer data from model, otherwise create new consumer
        if (empty($params['consumerKey']) || empty($params['consumerSecret'])) {
            $data = Mage::helper('apiextension')->createApiUserAndRole();
            if ($data) {
                $params['consumerKey'] = $data['consumerKey'];
                $params['consumerSecret'] = $data['consumerSecret'];
            }
        }
        
        $params['callback'] = rtrim($params['url'], '/') . '/newsletter2go/callback';
        return json_encode($params);
    }

    /**
     * @return string
     */
    public function getConnectUrl() {
        return self::N2GO_CONNECT_URL;
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => $this->helper('adminhtml')->__('Connect to Newsletter2Go'),
                'onclick' => 'javascript:n2go_connect(); return false;',
            ));

        return $button->toHtml();
    }
}

