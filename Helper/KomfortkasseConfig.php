<?php
namespace Ltc\Komfortkasse\Helper;

/**
 * Komfortkasse
 * Config Class
 * @version 1.9.5-Magento2 */
class KomfortkasseConfig
{
    const activate_export = 'sales/komfortkasse/activate_export';
    const activate_update = 'sales/komfortkasse/activate_update';
    const payment_methods = 'sales/komfortkasse/payment_methods';
    const status_open = 'sales/komfortkasse/status_open';
    const status_paid = 'sales/komfortkasse/status_paid';
    const status_cancelled = 'sales/komfortkasse/status_cancelled';
    const payment_methods_invoice = 'sales/komfortkasse/payment_methods_invoice';
    const status_open_invoice = 'sales/komfortkasse/status_open_invoice';
    const status_paid_invoice = 'sales/komfortkasse/status_paid_invoice';
    const status_cancelled_invoice = 'sales/komfortkasse/status_cancelled_invoice';
    const payment_methods_cod = 'sales/komfortkasse/payment_methods_cod';
    const status_open_cod = 'sales/komfortkasse/status_open_cod';
    const status_paid_cod = 'sales/komfortkasse/status_paid_cod';
    const status_cancelled_cod = 'sales/komfortkasse/status_cancelled_cod';
    const encryption = 'sales/komfortkasse/encryption';
    const accesscode = 'sales/komfortkasse/accesscode';
    const apikey = 'sales/komfortkasse/apikey';
    const publickey = 'sales/komfortkasse/publickey';
    const privatekey = 'sales/komfortkasse/privatekey';


    /**
     * Set Config.
     *
     *
     * @param string $constantKey Constant Key
     * @param string $value Value
     *
     * @return void
     */
    public static function setConfig($constantKey, $value)
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $om->get('\Magento\Config\Model\ResourceModel\Config')->saveConfig($constantKey, $value, \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $om->get('\Magento\Framework\App\ReinitableConfig')->reinit();
        $om->get('\Magento\Store\Model\StoreManagerInterface')->reinitStores();

    }

 // end setConfig()


    /**
     * Get Config.
     *
     *
     * @param string $constantKey Constant Key
     *
     * @return mixed
     */
    public static function getConfig($constantKey, $order=null)
    {
        $store_id = null;
        if ($order != null) {
            $store_id = $order['store_id'];
        } else {
            // export und update werden in den getId Methoden nochmals extra ber�cksichtigt.
            if ($constantKey == self::activate_export)
                return true;
            if ($constantKey == self::activate_update)
                return true;
        }

        $value = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\App\Config\ScopeConfigInterface')->getValue($constantKey, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store_id);
        if ($value === null)
            $value = ''; // to prevent Exception: Deprecated Functionality: str_replace(): Passing null to parameter #3 ($subject)
        return $value;

    }

 // end getConfig()


    /**
     * Get Request Parameter.
     *gut
     *
     * @param string $key Key
     *
     * @return string
     */
    public static function getRequestParameter($key)
    {
        return urldecode(\Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\App\Request\Http')->getParam($key));

    }

 // end getRequestParameter()


    /**
     * Get Magento Version.
     *
     *
     * @return string
     */
    public static function getVersion()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        return $productMetadata->getVersion();
    } // end getVersion()



    /**
     * Output
     *
     * @param mixed $s Data to output
     */
    public static function output($s)
    {
        // do nothing, just return $s. output has to be done in Controller/Main/index.php
        return $s;

    }

    // end output()

    public static function log($s) {
        // not using logging for now as logging cant be switched off per module
    }

}//end class