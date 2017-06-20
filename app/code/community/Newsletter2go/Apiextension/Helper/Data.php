<?php

class Newsletter2go_Apiextension_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Creates API user, role, assign user to the role and creates consumer
     *
     * @return array|boolean
     */
    public function createApiUserAndRole()
    {
        $result = array();

        try {
            //creating oAuth consumer token
            /* @var $oauthConsumer Mage_Oauth_Model_Consumer */
            $oauthConsumer = Mage::getModel('oauth/consumer')->getCollection()
                ->addFieldToFilter('name', 'Newsletter2Go-Consumer')
                ->getFirstItem();

            if (!$oauthConsumer || !$oauthConsumer->getId()) {
                $helper = Mage::helper('oauth');
                $oauthConsumer = Mage::getModel('oauth/consumer');
                $oauthConsumer->setName('Newsletter2Go-Consumer');
                $oauthConsumer->setKey($helper->generateConsumerKey());
                $oauthConsumer->setSecret($helper->generateConsumerSecret());
                $oauthConsumer->save();
            }

            $result['consumerName'] = 'Newsletter2Go-Consumer';
            $result['consumerKey'] = $oauthConsumer->getKey();
            $result['consumerSecret'] = $oauthConsumer->getSecret();

            $user = Mage::getSingleton('admin/session');
            $userId = $user->getUser()->getUserId();
            $apiRole = Mage::getModel('api2/acl_global_role')->getCollection()
                ->addFilterByAdminId($userId)
                ->getFirstItem();

            if (!$apiRole || !$apiRole->getId()) {
                $apiRole = Mage::getModel('api2/acl_global_role')->setRoleName('Newsletter2Go')->save();
                $resourceModel = Mage::getResourceModel('api2/acl_global_role');
                $resourceModel->saveAdminToRoleRelation($userId, $apiRole->getId());
            }

            //setting rest role
            $result['restRole'] = $apiRole->getRoleName();
            $id = $apiRole->getId();
            $rule = Mage::getModel('api2/acl_global_rule');
            $ruleCollection = $rule->getCollection()
                ->addFilterByRoleId($id)
                ->addFieldToFilter('privilege', 'retrieve')
                ->addFieldToFilter('resource_id', array('in' => 'all'));

            foreach ($ruleCollection as $singleRule) {
                $singleRule->delete();
            }

            $rule->setRoleId($id)->setResourceId('all')->setPrivilege('retrieve')->save();

            //setting attributes
            $attribute = Mage::getModel('api2/acl_filter_attribute');

            $attributeCollection = $attribute->getCollection()
                ->addFieldToFilter('user_type', 'admin')
                ->addFieldToFilter('operation', 'read')
                ->addFieldToFilter('resource_id', array('in' => 'all'));
            foreach ($attributeCollection as $singleAttribute) {
                $singleAttribute->delete();
            }

            $attribute->setUserType('admin')->setResourceId('all')->setOperation('read')->save();
            $attribute->setId(null)->isObjectNew(true);

            //saving configuration
            $configModel = Mage::getModel('core/config');
            $configModel->saveConfig('newsletter2go/apiextension/rest_role', $result['restRole'], 'default', 0);
            $configModel->saveConfig('newsletter2go/apiextension/consumer_name', $result['consumerName'], 'default', 0);
            $configModel->saveConfig('newsletter2go/apiextension/consumer_key', $result['consumerKey'], 'default', 0);
            $configModel->saveConfig(
                'newsletter2go/apiextension/consumer_secret',
                $result['consumerSecret'],
                'default',
                0
            );

        } catch (Exception $e) {
            return false;
        }

        return $result;
    }

    /**
     * Generates random string with $length characters
     *
     * @param int $length
     * @return string
     */
    public function generateRandomString($length = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }


}
