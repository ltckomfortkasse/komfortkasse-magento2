<?php

namespace Ltc\Komfortkasse\Helper;

/**
 * Komfortkasse Order Class
 * in KK, an Order is an Array providing the following members:
 * number, date, email, customer_number, payment_method, amount, currency_code, exchange_rate, language_code, invoice_number, store_id
 * status: data type according to the shop system
 * delivery_ and billing_: _firstname, _lastname, _company, _street, _postcode, _city, _countrycode
 * products: an Array of item numbers
 *
 * @version 1.9.12-Magento2
 */
class KomfortkasseOrder
{

    /**
     * Get open order IDs.
     *
     * @return string all order IDs that are "open" and relevant for transfer to kk
     */
    public static function getOpenIDs()
    {
        $ret = [ ];
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        foreach ($om->get('\Magento\Store\Model\StoreManagerInterface')->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {

                    $store_id = $store->getId();
                    $store_id_order = [ ];
                    $store_id_order ['store_id'] = $store_id;

                    if (!KomfortkasseConfig::getConfig(KomfortkasseConfig::activate_export, $store_id_order)) {
                        continue;
                    }

                    // PREPAYMENT

                    $openOrders = KomfortkasseConfig::getConfig(KomfortkasseConfig::status_open, $store_id_order);
                    $paymentMethods = KomfortkasseConfig::getConfig(KomfortkasseConfig::payment_methods, $store_id_order);

                    if (!empty($openOrders) && !empty($paymentMethods)) {
                        $openOrders = explode(',', $openOrders);
                        $paymentMethods = explode(',', $paymentMethods);

                        $salesModel = $om->create('\Magento\Sales\Model\Order');
                        $salesCollection = $salesModel->getCollection()->addAttributeToFilter('status',
                                [ 'in' => $openOrders
                                ])->addFieldToFilter('store_id', $store_id);

                        foreach ($salesCollection as $order) {
                            try {
                                $payment = $order->getPayment();
                                $method = $payment === null ? null : $payment->getMethodInstance()->getCode();
                                if (in_array($method, $paymentMethods, true) === true) {
                                    $orderId = $order->getIncrementId();
                                    $ret [] = $orderId;
                                }
                            } catch ( \Exception $e ) {
                                \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->critical(
                                        $e);
                            }
                        }

                        // Add all orders with unpaid invoices (in case the invoice is created before shipping).
                        $invoiceModel = $om->create('\Magento\Sales\Model\Order\Invoice');
                        $invoiceCollection = $invoiceModel->getCollection()->addAttributeToFilter('state',
                                \Magento\Sales\Model\Order\Invoice::STATE_OPEN)->addFieldToFilter('store_id', $store_id);
                        foreach ($invoiceCollection as $invoice) {
                            try {
                                $order = $invoice->getOrder();
                                $payment = $order->getPayment();
                                $method = $payment === null ? null : $payment->getMethodInstance()->getCode();
                                if (in_array($method, $paymentMethods, true) === true) {
                                    $orderId = $order->getIncrementId();
                                    if (in_array($orderId, $ret) === false) {
                                        $ret [] = $orderId;
                                    }
                                }
                            } catch ( \Exception $e ) {
                                \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->critical(
                                        $e);
                            }
                        }
                    }

                    // INVOICE

                    $openOrders = KomfortkasseConfig::getConfig(KomfortkasseConfig::status_open_invoice, $store_id_order);
                    $paymentMethods = KomfortkasseConfig::getConfig(KomfortkasseConfig::payment_methods_invoice,
                            $store_id_order);

                    if (!empty($openOrders) && !empty($paymentMethods)) {
                        $openOrders = explode(',', $openOrders);
                        $paymentMethods = explode(',', $paymentMethods);

                        $salesModel = $om->create('\Magento\Sales\Model\Order');
                        $salesCollection = $salesModel->getCollection()->addAttributeToFilter('status',
                                [ 'in' => $openOrders
                                ])->addFieldToFilter('store_id', $store_id);

                        foreach ($salesCollection as $order) {
                            try {
                                $payment = $order->getPayment();
                                $method = $payment === null ? null : $payment->getMethodInstance()->getCode();
                                if (in_array($method, $paymentMethods, true) === true) {
                                    $orderId = $order->getIncrementId();
                                    $ret [] = $orderId;
                                }
                            } catch ( \Exception $e ) {
                                \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->critical(
                                        $e);
                            }
                        }
                    }

                    // COD

                    $openOrders = KomfortkasseConfig::getConfig(KomfortkasseConfig::status_open_cod, $store_id_order);
                    $paymentMethods = KomfortkasseConfig::getConfig(KomfortkasseConfig::payment_methods_cod,
                            $store_id_order);

                    if (!empty($openOrders) && !empty($paymentMethods)) {
                        $openOrders = explode(',', $openOrders);
                        $paymentMethods = explode(',', $paymentMethods);

                        $salesModel = $om->create('\Magento\Sales\Model\Order');
                        $salesCollection = $salesModel->getCollection()->addAttributeToFilter('status',
                                [ 'in' => $openOrders
                                ])->addFieldToFilter('store_id', $store_id);

                        foreach ($salesCollection as $order) {
                            try {
                                $payment = $order->getPayment();
                                $method = $payment === null ? null : $payment->getMethodInstance()->getCode();
                                if (in_array($method, $paymentMethods, true) === true) {
                                    $orderId = $order->getIncrementId();
                                    $ret [] = $orderId;
                                }
                            } catch ( \Exception $e ) {
                                \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->critical(
                                        $e);
                            }
                        }
                    }
                }
            }
        }

        return $ret;
    }

    // end getOpenIDs()

    /**
     * Get refund IDS.
     *
     * @return string all refund IDs that are "open" and relevant for transfer to kk
     */
    public static function getRefundIDs()
    {
        $ret = [ ];
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        foreach ($om->get('\Magento\Store\Model\StoreManagerInterface')->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {

                    $store_id = $store->getId();
                    $store_id_order = [ ];
                    $store_id_order ['store_id'] = $store_id;

                    if (!KomfortkasseConfig::getConfig(KomfortkasseConfig::activate_export, $store_id_order)) {
                        continue;
                    }

                    $paymentMethods = explode(',',
                            KomfortkasseConfig::getConfig(KomfortkasseConfig::payment_methods, $store_id_order));
                    $paymentMethods = array_merge($paymentMethods,
                            explode(',',
                                    KomfortkasseConfig::getConfig(KomfortkasseConfig::payment_methods_cod,
                                            $store_id_order)));
                    $paymentMethods = array_merge($paymentMethods,
                            explode(',',
                                    KomfortkasseConfig::getConfig(KomfortkasseConfig::payment_methods_invoice,
                                            $store_id_order)));

                    $cmModel = $om->create('\Magento\Sales\Model\Order\Creditmemo');
                    $cmCollection = $cmModel->getCollection()->addFieldToFilter('store_id', $store_id);

                    foreach ($cmCollection as $creditMemo) {
                        if ($creditMemo->getTransactionId() == null) {
                            $order = $creditMemo->getOrder();
                            $method = $order->getPayment()->getMethodInstance()->getCode();
                            if (in_array($method, $paymentMethods, true) === true) {
                                $cmId = $creditMemo->getIncrementId();
                                $ret [] = $cmId;
                            }
                        }
                    }
                }
            }
        }

        return $ret;
    }

    // end getRefundIDs()

    /**
     * Get order.
     *
     * @param string $number order number
     *
     * @return array order
     */
    public static function getOrder($number)
    {
        if (empty($number) === true)
            return null;

        /** @var \Magento\Framework\ObjectManagerInterface $om */
        $om = \Magento\Framework\App\ObjectManager::getInstance();

        $isInvoiceNumber = str_starts_with($number, '{invoicenumber}');
        if ($isInvoiceNumber)
            $number = substr($number, strpos($number, '{invoicenumber}') + 15);

        if (empty($number) === true)
            return null;

        $order = null;
        if ($isInvoiceNumber) {
            $invoice = $om->create('\Magento\Sales\Model\Order\Invoice')->loadByIncrementId($number);
            if (empty($invoice) === true)
                return null;
            $order = $invoice->getOrder();
            if (empty($order) === true)
                return null;
        } else {
            $order = $om->create('\Magento\Sales\Model\Order')->loadByIncrementId($number);
            if (empty($order) === true)
                return null;
            if ($number != $order->getIncrementId())
                return null;
        }

        $conf_general = $om->get('\Magento\Framework\App\Config\ScopeConfigInterface')->getValue('general',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $ret = [ ];
        $ret ['number'] = $order->getIncrementId();
        $ret ['status'] = $order->getStatus();
        if ($order->getCreatedAt())
            $ret ['date'] = date('d.m.Y', strtotime($order->getCreatedAt()));
        $ret ['email'] = $order->getCustomerEmail();
        $ret ['customer_number'] = $order->getCustomerId();
        $ret ['payment_method'] = $order->getPayment() == null ? null : ($order->getPayment()->getMethodInstance() == null ? null : $order->getPayment()->getMethodInstance()->getCode());
        $ret ['amount'] = $order->getGrandTotal();
        $ret ['currency_code'] = $order->getOrderCurrencyCode();
        $ret ['exchange_rate'] = $order->getBaseToOrderRate();

        // Rechnungsnummer und -datum
        $invoiceColl = $order->getInvoiceCollection();
        if ($invoiceColl->getSize() > 0) {
            foreach ($order->getInvoiceCollection() as $invoice) {
                $ret ['invoice_number'] [] = $invoice->getIncrementId();
                $invoiceDate = date('d.m.Y', strtotime($invoice->getCreatedAt()));
                if (!isset($ret ['invoice_date']) || strtotime($ret ['invoice_date']) < strtotime($invoiceDate)) {
                    $ret ['invoice_date'] = $invoiceDate;
                }
            }
        }

        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress) {
            $ret ['delivery_firstname'] = self::myutf8_encode($shippingAddress->getFirstname());
            $ret ['delivery_lastname'] = self::myutf8_encode($shippingAddress->getLastname());
            $ret ['delivery_company'] = self::myutf8_encode($shippingAddress->getCompany());
            if (method_exists($shippingAddress, 'getStreetFull')) {
                $ret ['delivery_street'] = self::myutf8_encode($shippingAddress->getStreetFull());
            } elseif (method_exists($shippingAddress, 'getStreet')) {
                $street = $shippingAddress->getStreet();
                $ret ['delivery_street'] = self::myutf8_encode($street [0]);
            }
            $ret ['delivery_postcode'] = self::myutf8_encode($shippingAddress->getPostcode());
            $ret ['delivery_city'] = self::myutf8_encode($shippingAddress->getCity());
            $ret ['delivery_countrycode'] = self::myutf8_encode($shippingAddress->getCountryId());
            $ret ['delivery_phone'] = self::myutf8_encode($shippingAddress->getTelephone());
        }

        $billingAddress = $order->getBillingAddress();
        if ($billingAddress) {
            $ret ['language_code'] = substr($conf_general ['locale'] ['code'], 0, 2) . '-' . $billingAddress->getCountryId();
            $ret ['billing_firstname'] = self::myutf8_encode($billingAddress->getFirstname());
            $ret ['billing_lastname'] = self::myutf8_encode($billingAddress->getLastname());
            $ret ['billing_company'] = self::myutf8_encode($billingAddress->getCompany());
            if (method_exists($billingAddress, 'getStreetFull')) {
                $ret ['billing_street'] = self::myutf8_encode($billingAddress->getStreetFull());
            } elseif (method_exists($billingAddress, 'getStreet')) {
                $street = $billingAddress->getStreet();
                $ret ['billing_street'] = self::myutf8_encode($street [0]);
            }
            $ret ['billing_postcode'] = self::myutf8_encode($billingAddress->getPostcode());
            $ret ['billing_city'] = self::myutf8_encode($billingAddress->getCity());
            $ret ['billing_countrycode'] = self::myutf8_encode($billingAddress->getCountryId());
            $ret ['billing_phone'] = self::myutf8_encode($billingAddress->getTelephone());
        } else {
            $ret ['language_code'] = substr($conf_general ['locale'] ['code'], 0, 2);
        }

        foreach ($order->getAllItems() as $itemId => $item) {
            $sku = $item->getSku();
            if ($sku) {
                $ret ['products'] [] = $sku;
            } else {
                $ret ['products'] [] = $item->getName();
            }
        }

        $ret ['store_id'] = $order->getStoreId();

        /** @var \Magento\Framework\Module\Dir\Reader $reader */
        $reader = $om->get('\Magento\Framework\Module\Dir\Reader');
        $path = $reader->getModuleDir('', 'Ltc_Komfortkasse');
        $order_extension = false;
        if (file_exists("{$path}/Helper/KomfortkasseOrderExtension.php") === true) {
            $order_extension = true;
        }
        if ($order_extension && method_exists('KomfortkasseOrderExtension', 'extendOrder') === true) {
            $ret = KomfortkasseOrderExtension::extendOrder($order, $ret);
        }

        return $ret;
    }

    // end getOrder()

    /**
     * Get refund.
     *
     * @param string $number refund number
     *
     * @return array refund
     */
    public static function getRefund($number)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $om->get('\Magento\Framework\App\ResourceConnection');
        $id = $resource->getConnection('default')->fetchOne(
                'SELECT `entity_id` FROM `' . $resource->getTableName('sales_creditmemo') . "` WHERE `increment_id` = '" . $number . "'");

        $creditMemo = $om->create('\Magento\Sales\Model\Order\Creditmemo')->load($id);
        if (empty($number) === true || empty($creditMemo) === true || $number != $creditMemo->getIncrementId()) {
            return null;
        }

        $ret = [ ];
        $ret ['number'] = $creditMemo->getOrder()->getIncrementId();
        // Number of the Creditmemo.
        $ret ['customer_number'] = $creditMemo->getIncrementId();
        $ret ['date'] = date('d.m.Y', strtotime($creditMemo->getCreatedAt()));
        $ret ['amount'] = $creditMemo->getGrandTotal();

        return $ret;
    }

    // end getRefund()

    /**
     * Update order.
     *
     * @param array $order order
     * @param string $status status
     * @param string $callbackid callback ID
     *
     * @return void
     */
    public static function updateOrder($order, $status, $callbackid)
    {
        if (!KomfortkasseConfig::getConfig(KomfortkasseConfig::activate_update, $order)) {
            return;
        }

        $om = \Magento\Framework\App\ObjectManager::getInstance();

        // Hint: PAID and CANCELLED are supported as of now.
        $order = $om->create('\Magento\Sales\Model\Order')->loadByIncrementId($order ['number']);

        $om->get('\Magento\Framework\Event\Manager')->dispatch('komfortkasse_change_order_status_before',
                [ 'order' => $order,'status' => $status,'callbackid' => $callbackid
                ]);

        $stateCollection = $om->create('\Magento\Sales\Model\Order\Status')->getCollection()->joinStates();
        $stateCollection->addFieldToFilter('main_table.status', [ 'like' => $status
        ]);
        $state = $stateCollection->getFirstItem()->getState();

        if ($state == 'processing' || $state == 'closed' || $state == 'complete') {
            // If there is no invoice, capture() will create an invoice. If there is already an invoice, update the invoice.

            $invoiceColl = $order->getInvoiceCollection();
            if ($invoiceColl->getSize() == 0) {
                $payment = $order->getPayment();
                $payment->capture(null);

                if ($callbackid) {
                    $payment->setTransactionId($callbackid);
                    $transaction = $payment->addTransaction(
                            \Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);
                }

                $order->save();
                $invoiceColl = $order->getInvoiceCollection();
            }

            if ($invoiceColl->getSize() > 0) {
                foreach ($order->getInvoiceCollection() as $invoice) {
                    $invoice->pay();
                    $invoice->addComment($callbackid, false, false);
                    self::mysave($invoice);
                }
            }

//             $invoiceColl = $order->getInvoiceCollection();
//             if ($invoiceColl->getSize() > 0) {
//                 foreach ($order->getInvoiceCollection() as $invoice) {
//                     $invoice->pay();
//                     $invoice->addComment($callbackid, false, false);
//                     self::mysave($invoice);
//                 }
//             } else {
//                 $payment = $order->getPayment();
//                 $payment->capture(null);

//                 if ($callbackid) {
//                     $payment->setTransactionId($callbackid);
//                     $transaction = $payment->addTransaction(
//                             \Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);
//                 }
//             }

            $history = $order->addStatusHistoryComment('' . $callbackid, $status);
            $order->save();
        } elseif ($state == 'canceled') {
            if ($callbackid) {
                $history = $order->addStatusHistoryComment('' . $callbackid, $status);
            }
            if ($order->canCancel()) {
                $order->cancel();
            }
            $order->setStatus($status);
            $order->setState($state);
            $order->save();
        } else {
            if ($callbackid) {
                $history = $order->addStatusHistoryComment('' . $callbackid, $status);
            }
            $order->setStatus($status);
            $order->setState($state);
            $order->save();
        }

        $om->get('\Magento\Framework\Event\Manager')->dispatch('komfortkasse_change_order_status_after',
                [ 'order' => $order,'status' => $status,'callbackid' => $callbackid
                ]);
    }

    // end updateOrder()

    /**
     * Update order.
     *
     * @param string $refundIncrementId Increment ID of refund
     * @param string $status status
     * @param string $callbackid callback ID
     *
     * @return void
     */
    public static function updateRefund($refundIncrementId, $status, $callbackid)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();

        $resource = $om->get('\Magento\Framework\App\ResourceConnection');
        $id = $resource->getConnection('default')->fetchOne(
                'SELECT `entity_id` FROM `' . $resource->getTableName('sales_creditmemo') . "` WHERE `increment_id` = '" . $refundIncrementId . "'");

        $creditMemo = $om->create('\Magento\Sales\Model\Order\Creditmemo')->load($id);

        $store_id = $creditMemo->getStoreId();
        $store_id_order = [ ];
        $store_id_order ['store_id'] = $store_id;

        if (!KomfortkasseConfig::getConfig(KomfortkasseConfig::activate_update, $store_id_order)) {
            return;
        }

        if ($creditMemo->getTransactionId() == null) {
            $creditMemo->setTransactionId($callbackid);
        }

        $history = $creditMemo->addComment($status . ' [' . $callbackid . ']', false, false);

        $creditMemo->save();
    }

    // end updateRefund()

    /**
     * Call an object's save method
     *
     * @param unknown $object
     *
     * @return void
     */
    private static function mysave($object)
    {
        $object->save();
    }

    public static function getInvoicePdfPrepare()
    {
    }

    public static function getInvoicePdf($invoiceNumber)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        if ($invoiceNumber && $invoice = $om->create('\Magento\Sales\Model\Order\Invoice')->loadByIncrementId(
                $invoiceNumber)) {

            $mm = $om->get('\Magento\Framework\Module\Manager');

            // try mageplaza pdf invoice
            if ($mm->isEnabled('Mageplaza_PdfInvoice')) {
                $adminPdf = $om->get('\Mageplaza\PdfInvoice\Model\Api\AdminPdf');
                return $adminPdf->getPdfInvoice($invoice->getId());
            }

            // try Magento Standard
            $pdf = $om->create('\Magento\Sales\Model\Order\Pdf\Invoice')->getPdf([ ($invoice)
            ]);
            return $pdf->render();
        }
    }

    private static function myutf8_encode($string)
    {
        return $string === null ? null : utf8_encode($string);
    }
}//end class
