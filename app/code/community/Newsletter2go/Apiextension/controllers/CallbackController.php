<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Newsletter2go_Apiextension_CallbackController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $authKey = $this->getRequest()->getParam('auth_key');
        $accessToken = $this->getRequest()->getParam('access_token');
        $refreshToken = $this->getRequest()->getParam('refresh_token');
        $companyId = $this->getRequest()->getParam('int_id');

        if (isset($authKey)) {
            Mage::getModel('core/config')->saveConfig(
                'newsletter2go/authentication/auth_key',
                $authKey,
                'default',
                0
            );
        }

        if (isset($accessToken)) {
            Mage::getModel('core/config')->saveConfig(
                'newsletter2go/authentication/access_token',
                $accessToken,
                'default',
                0
            );
        }

        if (isset($refreshToken)) {
            Mage::getModel('core/config')->saveConfig(
                'newsletter2go/authentication/refresh_token',
                $refreshToken,
                'default',
                0
            );
        }

        if (isset($companyId)) {
            Mage::getModel('core/config')->saveConfig(
                'newsletter2go/authentication/company_id',
                $companyId,
                'default',
                0);
        }

        header('Content-Type: application/json;');
        exit(json_encode(array('success' => true)));
    }
}
