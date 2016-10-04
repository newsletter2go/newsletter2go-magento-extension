<?php

class Newsletter2go_Apiextension_Model_Api2_Subscriber_Fields_Rest_Admin_V1 extends Newsletter2go_Apiextension_Model_Api2_Subscriber_Fields
{

    public function getFields()
    {
        $result = array();
        /** @var Mage_Customer_Model_Customer $customerModel */
        $customerModel = Mage::getModel('customer/customer');
        $attributes = $customerModel->getAttributes();
        $attributeArray = array();

        /** @var Mage_Customer_Model_Attribute $a */
        foreach ($attributes as $a) {
            /** @var Mage_Eav_Model_Entity_Type $entityType */
            $entityType = $a->getEntityType();
            foreach ($entityType->getAttributeCodes() as $attributeName) {
                /** @var Mage_Eav_Model_Entity_Attribute $attribute */
                $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('customer', $attributeName);
                $label = $attribute->getFrontendLabel();
                switch ($attribute->getFrontendInput()) {
                    case 'boolean':
                        $type = 'Boolean';
                        break;
                    case 'date':
                        $type = 'Date';
                        break;
                    default:
                        $type = 'String';
                        break;
                }

                if ($label != null) {
                    $attributeArray[] = array(
                        'id' => $attributeName,
                        'name' => $label,
                        'description' => $label,
                        'type' => $type,
                    );
                }
            }

            break;
        }

        // $result[] = $this->createArray('totalorders', 'Count of total orders by customer', 'Count of total orders by customer', 'Integer');
        // $result[] = $this->createArray('totalrevenue', 'Total revenue of customer', 'Total revenue of customer', 'Float');
        // $result[] = $this->createArray('averagecartsize', 'Average cart size', 'Average cart size', 'Float');
        // $result[] = $this->createArray('lastorder', 'Last order', 'Last order', 'Date');
        $result[] = $this->createArray('entity_id', 'Customer Id.', 'Unique customer number', 'Integer');
        $result[] = $this->createArray('website_id', 'Website Id.', 'Unique website number', 'Integer');
        $result[] = $this->createArray('email', 'E-mail', 'E-mail address', 'String');
        $result[] = $this->createArray('group_id', 'Group Id.', 'Unique group number', 'Integer');
        $result[] = $this->createArray('created_at', 'Created at', 'Timestamp of creation', 'Date');
        $result[] = $this->createArray('updated_at', 'Updated at', 'Timestamp of last update', 'Date');
        $result[] = $this->createArray('dob', 'Date of birth', 'Date of birth', 'Date');
        $result[] = $this->createArray('disable_auto_group_change', 'Disable auto group change', 'Disable auto group change', 'Boolean');
        $result[] = $this->createArray('created_in', 'Created in', 'Place it was created admin side or by registration', 'String');
        $result[] = $this->createArray('suffix', 'Suffix', 'suffix', 'String');
        $result[] = $this->createArray('prefix', 'Prefix', 'Prefix', 'String');
        $result[] = $this->createArray('firstname', 'Firstname', 'Firstname', 'String');
        $result[] = $this->createArray('middlename', 'Middlename', 'middlename', 'String');
        $result[] = $this->createArray('lastname', 'Lastname', 'lastname', 'String');
        $result[] = $this->createArray('taxvat', 'Tax VAT', 'Tax VAT', 'String');
        $result[] = $this->createArray('store_id', 'Store Id.', 'Unique store number', 'Integer');
        $result[] = $this->createArray('gender', 'Gender', 'Gender', 'Integer');
        $result[] = $this->createArray('is_active', 'Is active', 'Is Active', 'Boolean');
        $result[] = $this->createArray('subscriber_status', 'Subscriber status', 'Subscriber status', 'Integer');
        $result[] = $this->createArray('default_billing', 'Default billing address', 'Default billing address', 'Object');
        $result[] = $this->createArray('default_shipping', 'Default shipping address', 'Default shipping address', 'Object');

        // Union of static fields and custom attributes
        $results = array_merge(array_udiff($attributeArray, $result, function ($attributeArray, $result) {
            return strcmp($attributeArray['id'], $result['id']);
        }), $result);

        return $results;
    }

    protected function createArray($id, $name, $description, $type)
    {
        return array(
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'type' => $type,
        );
    }

    /**
     * Retrieve list of customers.
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        return $this->getFields();
    }

}
