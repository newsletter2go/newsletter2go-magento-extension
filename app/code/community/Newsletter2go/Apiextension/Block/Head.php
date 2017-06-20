<?php

class Newsletter2go_Apiextension_Block_Head extends Mage_Core_Block_Template
{
    /**
     *  Render tracking script if order id is set
     *
     * @return string
     */
    public function renderTracking()
    {
        $order = null;
        if ($this->getOrderId()) {
            $trackSelect = Mage::getStoreConfig('newsletter2go/track/tracking');
            $companyId = Mage::getStoreConfig('newsletter2go/authentication/company_id');

            // if tracking is enabled and company ID set add tracking script
            if ($trackSelect === '0' && !empty($companyId)) {

                // retrieve order information and create string
                $order = $this->getOrderInfo($this->getOrderId());
                $orderString = 'n2g("ecommerce:addTransaction", ' . json_encode($order) . ');';

                // retrieve order's items information and create string
                $itemsData = $this->getOrderData($this->getOrderId());
                $items = $this->formatItems($itemsData);

                // generate tracking script with order and item info
                $script = '<script id="n2g_script">
            !function(e,t,n,c,r,a,i){e.Newsletter2GoTrackingObject=r,e[r]=e[r]||function(){(e[r].q=e[r].q||[]).
            push(arguments)},e[r].l=1*new Date,a=t.createElement(n),i=t.getElementsByTagName(n)[0],a.async=1,a.src=c,i.
            parentNode.insertBefore(a,i)}(window,document,"script","//static-sandbox.newsletter2go.com/utils.js","n2g");
            n2g("create", "' . $companyId . '");' . $orderString . $items . 'n2g("ecommerce:send");</script>';

                return $script;
            }
        }
    }

    /**
     * Retrieves order information
     *
     * @param $orderId
     * @return array
     */
    private function getOrderInfo($orderId)
    {
        $result = array();
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', array('in' => array($orderId)));
        $store = Mage::app()->getStore();
        foreach ($collection as $order) {
            $result = array(
                'id' => $orderId,
                'affiliation' => $store->getName(),
                'revenue' => strval(round($order->getBaseGrandTotal(), 2)),
                'shipping' => strval(round($order->getShippingAmount(), 2)),
                'tax' => strval(round($order->getTaxAmount(), 2)),
            );
        }

        return $result;
    }

    /**
     * Retrieves order's item information
     *
     * @param $orderId
     * @return array
     */
    private function getOrderData($orderId)
    {
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('entity_id', array('in' => array($orderId)));
        $result = array();
        foreach ($collection as $order) {
            foreach ($order->getAllVisibleItems() as $product) {
                $temp = array(
                    'id' => $orderId,
                    'name' => $product->getName(),
                    'sku' => $product->getSku(),
                    'category' => '',
                    'price' => strval(round($product->getBasePrice(), 2)),
                    'quantity' => strval(round($product->getQtyOrdered(), 2)),
                );

                // add categories
                $categories = array();
                $productModel = Mage::getModel('catalog/product')->load($product->getProductId());
                foreach ($productModel->getCategoryIds() as $categoryId) {
                    $category = Mage::getModel('catalog/category')->load($categoryId);
                    $name = $category->getName();
                    $categories[] = $name;
                }

                $temp['category'] = implode(',', $categories);
                $result[] = $temp;
            }
        }

        return $result;
    }

    /**
     * Returns string with json encoded items data
     *
     * @param array $itemsData
     * @return string
     */
    private function formatItems($itemsData)
    {
        $result = '';
        foreach ($itemsData as $item) {
            $item = json_encode($item);
            $result .= 'n2g("ecommerce:addItem", ' . $item . ');';
        }

        return $result;
    }
}
