<?php

class Newsletter2go_Apiextension_Model_Observer
{
    protected $orderIds;

    /**
     * @param   Varien_Event_Observer $observer
     * @return  Newsletter2go_Apiextension_Model_Observer
     */
    public function trackBuy($observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }

        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('newsletter2go.apiextension');
        if ($block) {
            $block->setOrderId(end($orderIds));
        }
    }
}
