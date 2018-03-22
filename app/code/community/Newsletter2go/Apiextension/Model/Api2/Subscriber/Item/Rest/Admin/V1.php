<?php

class Newsletter2go_Apiextension_Model_Api2_Subscriber_Item_Rest_Admin_V1 extends Newsletter2go_Apiextension_Model_Api2_Subscriber_item
{

    protected function getAdditionalData($product, &$data)
    {
        //$product = $this->getProduct();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {

            if ($attribute->getIsVisibleOnFront()) {
                $value = $attribute->getFrontend()->getValue($product);

                if (is_string($value) && strlen($value)) {
                    $data[$attribute->getAttributeCode() . '_label'] = $attribute->getStoreLabel();
                    $data[$attribute->getAttributeCode() . '_value'] = $value;

                }
            }
        }
    }

    /**
     * Retrieves list of item attributes
     */
    public function _retrieve()
    {
        /** @var Mage_Catalog_Model_Product $productModel */
        $productModel = Mage::getModel('catalog/product');
        $attributes = $productModel->getAttributes();
        $attributeArray = array();

        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $a */
        foreach ($attributes as $a) {
            /** @var Mage_Eav_Model_Entity_Type $entityType */
            $entityType = $a->getEntityType();
            foreach ($entityType->getAttributeCodes() as $attributeName) {
                /** @var Mage_Eav_Model_Entity_Attribute $attribute */
                $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeName);
                switch ($attribute->getFrontendInput()) {
                    case 'price':
                        $type = 'Float';
                        break;
                    case 'gallery':
                        $type = 'Array';
                        break;
                    case 'date':
                        $type = 'Date';
                        break;
                    default:
                        $type = 'String';
                        break;
                }

                $attributeArray[] = array(
                    'name' => $attributeName,
                    'type' => $type,
                );
            }

            break;
        }

        return array('items' => $attributeArray);
    }

    /**
     * Retrieve list of customers.
     *
     * @return mixed
     */
    protected function _retrieveCollection()
    {
        $id = $this->getRequest()->getParam('id');
        $storeCode = $this->getRequest()->getParam('store');

        $add_store = '';
        $model = Mage::getModel('catalog/product'); //getting product model
        if ($storeCode != null) {

            $storeId = Mage::getModel('core/store')->load($storeCode, 'code')->getId();
            $model->setStoreId($storeId);
            $add_store = '?___store=' . $storeCode;
        } else {
            $storeId = Mage::app()
                ->getWebsite()
                ->getDefaultGroup()
                ->getDefaultStoreId();
            $model->setStoreId($storeId);
        }

        $_product = false;

        if (is_numeric($id)) {
            $_product = $model->load($id); //getting product object for particular product id
        }

        if (!$_product || !$_product->getId()) {// !isset($_product->getData()['sku'])) {
            $_product = $model->loadByAttribute('sku', $id);
            // return array('error' => 'int-0-600', 'message' => serialize($_product->getData()));

        }

        if (!$_product || !$_product->getId()) {// !isset($_product->getData()['sku'])) {

            return array('error' => 'int-0-600', 'message' => 'product not found');
        }

        $p = $_product->getData();

        $this->getAdditionalData($_product, $p);

        $p['product_url_1'] = $_product->getUrlPath();
        if (strlen($add_store) > 0) {
            if (strpos($p['product_url_1'], $add_store) === false) {
                $p['product_url_1'] = $p['product_url_1'] . $add_store;
            }
        }
        $p['product_url_2'] = $_product->getProductUrl();

        $p['product_url_3'] = $_product->getUrlInStore();

        if (isset($p['price'])) {
            $p['price'] = number_format((float)$p['price'], 2);
        }
        if (isset($p['special_price'])) {
            $p['special_price'] = number_format((float)$p['special_price'], 2);
        }
        if (isset($p['msrp'])) {
            $p['msrp'] = number_format((float)$p['msrp'], 2);
        }

        return array('items' => array($p));
    }

}