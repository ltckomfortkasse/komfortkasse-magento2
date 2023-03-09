<?php
// namespace needed for magento2!
namespace Ltc\Komfortkasse\Helper;

// require_once is not allowed, classes are loaded in index.php
// require_once 'KomfortkasseConfig.php';
// require_once 'KomfortkasseOrder.php';

/**
 * Komfortkasse
 * Main Class, multi-shop
 */
class Komfortkasse
{
    const PLUGIN_VER = '1.9.10';
    const MAXLEN_SSL = 117;
    const LEN_MCRYPT = 16;


    /**
     * Read orders.
     *
     * @return void
     */
    public static function readorders()
    {
        return Komfortkasse::read(false);

    }

    // end readorders()


    /**
     * Read refunds.
     *
     * @return void
     */
    public static function readrefunds()
    {
        return Komfortkasse::read(true);

    }

    // end readrefunds()


    /**
     * Read orders/refunds.
     *
     * @param bool $refunds if refunds should be read.
     *
     * @return void
     */
    public static function read($refunds)
    {

        if (!KomfortkasseConfig::getConfig(KomfortkasseConfig::activate_export)) {
            return;
        }

        if (Komfortkasse::check() === false) {
            return;
        }

        // Schritt 1: alle IDs ausgeben.
        $param = KomfortkasseConfig::getRequestParameter('o');
        $param = Komfortkasse::kkdecrypt($param);

        if ($param === 'all') {
            $o = '';
            if ($refunds === true) {
                $ids = KomfortkasseOrder::getRefundIDs();
            } else {
                $ids = KomfortkasseOrder::getOpenIDs();
            }

            foreach ($ids as $id) {
                $o = $o . Komfortkasse::kk_csv($id);
            }

            return KomfortkasseConfig::output(Komfortkasse::kkencrypt($o));
        } else {
            $o = '';
            $ex = explode(';', $param);
            foreach ($ex as $id) {
                $id = trim($id);
                // Schritt 2: details pro auftrag ausgeben.
                if ($refunds === true) {
                    $order = KomfortkasseOrder::getRefund($id);
                } else {
                    $order = KomfortkasseOrder::getOrder($id);
                    if (!$order) {
                        continue;
                    }
                    if ($order['payment_method'])
                        $order['type'] = self::getOrderType($order);
                }

                if (!$order) {
                    continue;
                }

                $o = $o . http_build_query($order);
                $o = $o . "\n";
            }

            $cry = Komfortkasse::kkencrypt($o);
            if ($cry === false) {
                return KomfortkasseConfig::output(Komfortkasse::kkcrypterror());
            } else {
                return KomfortkasseConfig::output($cry);
            }
        }
        // end if
    }

    // end read()


    /**
     * Test.
     *
     * @return void
     */
    public static function test()
    {
        $dec = Komfortkasse::kkdecrypt(KomfortkasseConfig::getRequestParameter('test'));

        $enc = Komfortkasse::kkencrypt($dec);

        return KomfortkasseConfig::output($enc);

    }

    // end test()


    /**
     * Init.
     *
     * @return void
     */
    public static function init()
    {
        $ret = '';

        $ret .= 'connection:connectionsuccess|';

        $ret .= 'accesskey:';
        // Set access code.
        $hashed = hash('md5', KomfortkasseConfig::getRequestParameter('accesscode'));
        $current = KomfortkasseConfig::getConfig(KomfortkasseConfig::accesscode);
        if ($current != '' && $current !== 'undefined' && $current != $hashed) {
            $ret .= ('Access Code already set! Shop ' . $current . ', given (hash) ' . $hashed);
            return KomfortkasseConfig::output($ret);
        }

        if ($hashed != KomfortkasseConfig::getRequestParameter('accesscode_hash')) {
            $ret .= ('MD5 Hashes do not match! Shop ' . $hashed . ' given ' . KomfortkasseConfig::getRequestParameter('accesscode_hash'));
            return KomfortkasseConfig::output($ret);
        }

        KomfortkasseConfig::setConfig(KomfortkasseConfig::accesscode, $hashed);
        $ret .= ('accesskeysuccess|');

        $ret .= ('apikey:');
        // Set API key.
        $apikey = KomfortkasseConfig::getRequestParameter('apikey');
        if (KomfortkasseConfig::getConfig(KomfortkasseConfig::apikey) != '' && KomfortkasseConfig::getConfig(KomfortkasseConfig::apikey) !== 'undefined' && KomfortkasseConfig::getConfig(KomfortkasseConfig::apikey) !== $apikey) {
            $ret .= ('API Key already set! Shop ' . KomfortkasseConfig::getConfig(KomfortkasseConfig::apikey) . ', given ' . $apikey);
            return KomfortkasseConfig::output($ret);
        }

        KomfortkasseConfig::setConfig(KomfortkasseConfig::apikey, $apikey);
        $ret .= ('apikeysuccess|');

        $ret .= ('encryption:');
        $encryptionstring = null;
        // Look for openssl encryption.

        if (extension_loaded('openssl') === true) {

            // Look for public&privatekey encryption.
            $kpriv = KomfortkasseConfig::getRequestParameter('privateKey');
            $kpub = KomfortkasseConfig::getRequestParameter('publicKey');
            KomfortkasseConfig::setConfig(KomfortkasseConfig::privatekey, $kpriv);
            KomfortkasseConfig::setConfig(KomfortkasseConfig::publickey, $kpub);

            // Try with rsa.
            $crypttest = KomfortkasseConfig::getRequestParameter('testSSLEnc');
            $decrypt = Komfortkasse::kkdecrypt($crypttest, 'openssl');
            if ($decrypt === 'Can you hear me?') {
                $encryptionstring = 'openssl#' . OPENSSL_VERSION_TEXT . '#' . OPENSSL_VERSION_NUMBER . '|';
                KomfortkasseConfig::setConfig(KomfortkasseConfig::encryption, 'openssl');
            }
        }

        if (!$encryptionstring && extension_loaded('mcrypt') === true) {
            // Look for mcrypt encryption.
            $sec = KomfortkasseConfig::getRequestParameter('mCryptSecretKey');
            $iv = KomfortkasseConfig::getRequestParameter('mCryptIV');
            KomfortkasseConfig::setConfig(KomfortkasseConfig::privatekey, $sec);
            KomfortkasseConfig::setConfig(KomfortkasseConfig::publickey, $iv);

            // Try with mcrypt.
            $crypttest = KomfortkasseConfig::getRequestParameter('testMCryptEnc');
            $decrypt = Komfortkasse::kkdecrypt($crypttest, 'mcrypt');
            if ($decrypt === 'Can you hear me?') {
                $encryptionstring = 'mcrypt|';
                KomfortkasseConfig::setConfig(KomfortkasseConfig::encryption, 'mcrypt');
            }
        }

        // Fallback: base64.
        if (!$encryptionstring) {
            // Try with base64 encoding.
            $crypttest = KomfortkasseConfig::getRequestParameter('testBase64Enc');
            $decrypt = Komfortkasse::kkdecrypt($crypttest, 'base64');
            if ($decrypt === 'Can you hear me?') {
                $encryptionstring = 'base64|';
                KomfortkasseConfig::setConfig(KomfortkasseConfig::encryption, 'base64');
            }
        }

        if (!$encryptionstring) {
            $encryptionstring = 'ERROR:no encryption possible|';
        }

        $ret .= ($encryptionstring);

        $ret .= ('decryptiontest:');
        $decrypt = Komfortkasse::kkdecrypt($crypttest, KomfortkasseConfig::getConfig(KomfortkasseConfig::encryption));
        if ($decrypt === 'Can you hear me?') {
            $ret .= ('ok');
        } else {
            $ret .= (Komfortkasse::kkcrypterror());
        }

        $ret .= ('|encryptiontest:');
        $encrypt = Komfortkasse::kkencrypt('Yes, I see you!', KomfortkasseConfig::getConfig(KomfortkasseConfig::encryption));
        if ($encrypt !== false) {
            $ret .= ($encrypt);
        } else {
            $ret .= (Komfortkasse::kkcrypterror());
        }

        return KomfortkasseConfig::output($ret);
    }

    // end init()


    /**
     * Update orders.
     *
     * @return void
     */
    public static function updateorders()
    {
        return Komfortkasse::update(false);

    }

    // end updateorders()


    /**
     * Update refunds.
     *
     * @return void
     */
    public static function updaterefunds()
    {
        return Komfortkasse::update(true);

    }

    // end updaterefunds()


    /**
     * Update refunds or order.
     *
     * @param bool $refunds if refunds should be updated.
     *
     * @return void
     */
    public static function update($refunds)
    {
        if (!KomfortkasseConfig::getConfig(KomfortkasseConfig::activate_update)) {
            return;
        }

        if (Komfortkasse::check() === false) {
            return;
        }

        $param = KomfortkasseConfig::getRequestParameter('o');
        $param = Komfortkasse::kkdecrypt($param);

        if ($refunds === false) {
            $openids = KomfortkasseOrder::getOpenIDs();
        }

        $o = '';
        $lines = explode("\n", $param);

        foreach ($lines as $line) {
            $col = explode(';', $line);

            $count = Komfortkasse::mycount($col);
            $id = trim($col [0]);
            if ($count > 1) {
                $status = trim($col [1]);
            } else {
                $status = null;
            }

            if ($count > 2) {
                $callbackid = trim($col [2]);
            } else {
                $callbackid = null;
            }

            if (empty($id) === true || empty($status) === true) {
                continue;
            }

            if ($refunds === true) {
                KomfortkasseOrder::updateRefund($id, $status, $callbackid);
            } else {

                $order = KomfortkasseOrder::getOrder($id);
                if (!$order) {
                    continue;
                }
                if ($id != $order ['number']) {
                    continue;
                }

                $newstatus = Komfortkasse::getNewStatus($status, $order);
                if (empty($newstatus) === true) {
                    if ($status == 'PAID' && method_exists(KomfortkasseOrder, 'setPaid')) {
                        KomfortkasseOrder::setPaid($order, $callbackid);
                        $o = $o . Komfortkasse::kk_csv($id);
                    }
                    continue;
                }

                // only update if order status update is necessary (dont update if order has been updated manually)
                if ($order['status'] == $newstatus) {
                    $o = $o . Komfortkasse::kk_csv($id);
                    continue;
                }

                // dont update if order is no longer relevant (will be marked as DISAPPEARED later on)
                $updateOk = in_array($order ['number'], $openids);
                if ($updateOk === false) {
                    // setting from CANCELLED to PAID is allowed if not open
                    if ($status == 'PAID' && $order['status'] == Komfortkasse::getNewStatus('CANCELLED', $order))
                        $updateOk = true;
                }
                if ($updateOk === false)
                    continue;

                KomfortkasseOrder::updateOrder($order, $newstatus, $callbackid);
            }

            $o = $o . Komfortkasse::kk_csv($id);
        } // end foreach

        $cry = Komfortkasse::kkencrypt($o);
        if ($cry === false) {
            return KomfortkasseConfig::output(Komfortkasse::kkcrypterror());
        } else {
            return KomfortkasseConfig::output($cry);
        }

    }

    // end update()

    private static function isOpen($order)
    {
        $status = '';
        switch ($order ['type']) {
            case 'PREPAYMENT' :
                $status = KomfortkasseConfig::getConfig(KomfortkasseConfig::status_open, $order);
                break;
            case 'INVOICE' :
                $status = KomfortkasseConfig::getConfig(KomfortkasseConfig::status_open_invoice, $order);
                break;
            case 'COD' :
                $status = KomfortkasseConfig::getConfig(KomfortkasseConfig::status_open_cod, $order);
                break;
            default:
                return false;
        }

        return in_array($order['status'], explode(',', trim(str_replace('"', '', $status))));
    }

    /**
     * Notify order.
     *
     * @param unknown $id Order ID
     *
     * @return void
     */
    public static function notifyorder($id)
    {
        KomfortkasseConfig::log('notifyorder BEGIN');
        if (!KomfortkasseConfig::getConfig(KomfortkasseConfig::activate_export)) {
            KomfortkasseConfig::log('notifyorder END: global config not active');
            return;
        }

        $order = KomfortkasseOrder::getOrder($id);
        if (!$order) {
            return;
        }
        $order['type'] = self::getOrderType($order);
        if (!$order['type'])
            return;
        if (!KomfortkasseConfig::getConfig(KomfortkasseConfig::activate_export, $order)) {
            KomfortkasseConfig::log('notifyorder END: order config not active');
            return;
        }
        // See if order is relevant.
        if (!self::isOpen($order)) {
            KomfortkasseConfig::log('notifyorder END: order not open (1)');
            return;
        }
        if (method_exists ('KomfortkasseOrder', 'isOpen') && !KomfortkasseOrder::isOpen($order)) {
            KomfortkasseConfig::log('notifyorder END: order not open (2)');
            return;
        }

        $queryRaw = http_build_query($order);

        $queryEnc = Komfortkasse::kkencrypt($queryRaw);

        $query = http_build_query(array ('q' => $queryEnc,'hash' => KomfortkasseConfig::getConfig(KomfortkasseConfig::accesscode, $order),'key' => KomfortkasseConfig::getConfig(KomfortkasseConfig::apikey, $order)
        ));

        $contextData = array ('method' => 'POST','timeout' => 2,'header' => "Content-Type: application/x-www-form-urlencoded\r\n". "Connection: close\r\n" . 'Content-Length: ' . strlen($query) . "\r\n",'content' => $query
        );

        $context = stream_context_create(array ('http' => $contextData
        ));

        // Development: http://localhost:8080/kkos01/api...
        $result = file_get_contents('https://ssl.komfortkasse.eu/api/shop/neworder.jsf', false, $context);
    }

    // end notifyorder()


    /**
     * Info.
     *
     * @return void
     */
    public static function info()
    {
        if (Komfortkasse::check() === false) {
            return;
        }

        $version = KomfortkasseConfig::getVersion();

        $o = '';
        $o = $o . Komfortkasse::kk_csv($version);
        $o = $o . Komfortkasse::kk_csv(Komfortkasse::PLUGIN_VER);

        $cry = Komfortkasse::kkencrypt($o);
        if ($cry === false) {
            return KomfortkasseConfig::output(Komfortkasse::kkcrypterror());
        } else {
            return KomfortkasseConfig::output($cry);
        }

    }

    // end info()


    /**
     * Retrieve new status.
     *
     * @param unknown $status Status
     *
     * @return mixed
     */
    protected static function getNewStatus($status, $order)
    {

        $orderType = self::getOrderType($order);

        switch ($orderType) {
            case 'PREPAYMENT' :
                switch ($status) {
                    case 'PAID' :
                        return KomfortkasseConfig::getConfig(KomfortkasseConfig::status_paid, $order);
                    case 'CANCELLED' :
                        return KomfortkasseConfig::getConfig(KomfortkasseConfig::status_cancelled, $order);
                }
                return null;
            case 'INVOICE' :
                switch ($status) {
                    case 'PAID' :
                        return KomfortkasseConfig::getConfig(KomfortkasseConfig::status_paid_invoice, $order);
                    case 'CANCELLED' :
                        return KomfortkasseConfig::getConfig(KomfortkasseConfig::status_cancelled_invoice, $order);
                }
                return null;
            case 'COD' :
                switch ($status) {
                    case 'PAID' :
                        return KomfortkasseConfig::getConfig(KomfortkasseConfig::status_paid_cod, $order);
                    case 'CANCELLED' :
                        return KomfortkasseConfig::getConfig(KomfortkasseConfig::status_cancelled_cod, $order);
                }
                return null;
        }

    }

    // end getNewStatus()


    /**
     * Check.
     *
     * @return boolean
     */
    public static function check()
    {
        $ac = KomfortkasseConfig::getRequestParameter('accesscode');

        if (!$ac || hash('md5', $ac) !== KomfortkasseConfig::getConfig(KomfortkasseConfig::accesscode)) {
            return false;
        } else {
            return true;
        }

    }

    // end check()


    /**
     * Encrypt.
     *
     * @param string $s String to encrypt
     * @param string $encryption encryption method
     * @param string $keystring key string
     *
     * @return mixed
     *
     */
    protected static function kkencrypt($s, $encryption = null, $keystring = null)
    {
        if (!$encryption) {
            $encryption = KomfortkasseConfig::getConfig(KomfortkasseConfig::encryption);
        }
        if (!$keystring) {
            $keystring = KomfortkasseConfig::getConfig(KomfortkasseConfig::publickey);
        }
        if ($s === '') {
            return '';
        }

        switch ($encryption) {
            case 'openssl' :
                return Komfortkasse::kkencrypt_openssl($s, $keystring);
            case 'mcrypt' :
                return Komfortkasse::kkencrypt_mcrypt($s);
            case 'base64' :
                return Komfortkasse::kkencrypt_base64($s);
        }

    }

    // end kkencrypt()


    /**
     * Decrypt.
     *
     *
     * @param string $s String to decrypt
     * @param string $encryption encryption method
     * @param string $keystring key string
     *
     * @return Ambigous <boolean, string>|string
     */
    public static function kkdecrypt($s, $encryption = null, $keystring = null)
    {
        if (!$encryption) {
            $encryption = KomfortkasseConfig::getConfig(KomfortkasseConfig::encryption);
        }
        if (!$keystring) {
            $keystring = KomfortkasseConfig::getConfig(KomfortkasseConfig::privatekey);
        }
        if ($s === '') {
            return '';
        }

        switch ($encryption) {
            case 'openssl' :
                return Komfortkasse::kkdecrypt_openssl($s, $keystring);
            case 'mcrypt' :
                return Komfortkasse::kkdecrypt_mcrypt($s);
            case 'base64' :
                return Komfortkasse::kkdecrypt_base64($s);
        }

    }

    // end kkdecrypt()


    /**
     * Show encryption/decryption error.
     *
     * @param string $encryption encryption method
     *
     * @return mixed
     */
    protected static function kkcrypterror($encryption)
    {
        if (!$encryption) {
            $encryption = KomfortkasseConfig::getConfig(KomfortkasseConfig::encryption);
        }

        switch ($encryption) {
            case 'openssl' :
                return str_replace(':', ';', openssl_error_string());
        }

    }

    // end kkcrypterror()


    /**
     * Encrypt with base 64.
     *
     * @param string $s String to encrypt
     *
     * @return string decrypted string
     */
    protected static function kkencrypt_base64($s)
    {
        return Komfortkasse::mybase64_encode($s);

    }

    // end kkencrypt_base64()


    /**
     * Decrypt with base 64.
     *
     * @param string $s String to decrypt
     *
     * @return string decrypted string
     */
    protected static function kkdecrypt_base64($s)
    {
        return Komfortkasse::mybase64_decode($s);

    }

    // end kkdecrypt_base64()


    /**
     * Encrypt with mcrypt.
     *
     * @param string $s String to encrypt
     *
     * @return string decrypted string
     */
    protected static function kkencrypt_mcrypt($s)
    {
        $key = KomfortkasseConfig::getConfig(KomfortkasseConfig::privatekey);
        $iv = KomfortkasseConfig::getConfig(KomfortkasseConfig::publickey);
        $td = call_user_func('mcrypt_module_open', 'rijndael-128', ' ', 'cbc', $iv);
        $init = call_user_func('mcrypt_generic_init', $td, $key, $iv);

        $padlen = ((strlen($s) + Komfortkasse::LEN_MCRYPT) % Komfortkasse::LEN_MCRYPT);
        $s = str_pad($s, (strlen($s) + $padlen), ' ');
        $encrypted = call_user_func('mcrypt_generic', $td, $s);

        call_user_func('mcrypt_generic_deinit', $td);
        call_user_func('mcrypt_module_close', $td);

        return Komfortkasse::mybase64_encode($encrypted);

    }

    // end kkencrypt_mcrypt()


    /**
     * Encrypt with open ssl.
     *
     * @param string $s String to encrypt
     * @param string $keystring Key string
     *
     * @return string decrypted string
     */
    protected static function kkencrypt_openssl($s, $keystring)
    {
        $ret = '';

        $pubkey = "-----BEGIN PUBLIC KEY-----\n" . chunk_split($keystring, 64, "\n") . "-----END PUBLIC KEY-----\n";
        $key = openssl_get_publickey($pubkey);
        if ($key === false) {
            return false;
        }

        do {
            $current = substr($s, 0, Komfortkasse::MAXLEN_SSL);
            $s = substr($s, Komfortkasse::MAXLEN_SSL);
            if (openssl_public_encrypt($current, $encrypted, $key) === false) {
                return false;
            }

            $ret = $ret . "\n" . Komfortkasse::mybase64_encode($encrypted);
        } while ($s);

        openssl_free_key($key);
        return $ret;

    }

    // end kkencrypt_openssl()


    /**
     * Decrypt with open ssl.
     *
     * @param string $s String to decrypt
     * @param string $keystring Key string
     *
     * @return string decrypted string
     */
    protected static function kkdecrypt_openssl($s, $keystring)
    {
        $ret = '';

        $privkey = "-----BEGIN RSA PRIVATE KEY-----\n" . chunk_split($keystring, 64, "\n") . "-----END RSA PRIVATE KEY-----\n";
        $key = openssl_get_privatekey($privkey);
        if ($key === false) {
            return false;
        }

        $parts = explode("\n", $s);
        foreach ($parts as $part) {
            if ($part) {
                if (openssl_private_decrypt(Komfortkasse::mybase64_decode($part), $decrypted, $key) === false) {
                    return false;
                }
                $ret = $ret . $decrypted;
            }
        }

        openssl_free_key($key);
        return $ret;

    }

    // end kkdecrypt_openssl()


    /**
     * Decrypt with mcrypt.
     *
     * @param string $s String to decrypt
     *
     * @return string decrypted string
     */
    protected static function kkdecrypt_mcrypt($s)
    {
        $key = KomfortkasseConfig::getConfig(KomfortkasseConfig::privatekey);
        $iv = KomfortkasseConfig::getConfig(KomfortkasseConfig::publickey);
        $td = call_user_func('mcrypt_module_open', 'rijndael-128', ' ', 'cbc', $iv);
        $init = call_user_func('mcrypt_generic_init', $td, $key, $iv);

        $ret = '';

        $parts = explode("\n", $s);
        foreach ($parts as $part) {
            if ($part) {
                $decrypted = call_user_func('mdecrypt_generic', $td, Komfortkasse::mybase64_decode($part));
                $ret = $ret . trim($decrypted);
            }
        }

        call_user_func('mcrypt_generic_deinit', $td);
        call_user_func('mcrypt_module_close', $td);
        return $ret;

    }

    // end kkdecrypt_mcrypt()


    /**
     * Output CSV.
     *
     * @param string $s String to output
     *
     * @return string CSV
     */
    protected static function kk_csv($s)
    {
        return '"' . str_replace('"', '', str_replace(';', ',', utf8_encode($s))) . '";';

    }

    // end kk_csv()


    /**
     * Count
     *
     * @param array $array Arrays
     *
     * @return int count
     */
    protected static function mycount($array)
    {
        return count($array);

    }

    // end mycount()
    protected static function mybase64_decode($s)
    {
        return base64_decode($s);

    }

    // end mybase64_decode()
    protected static function mybase64_encode($s)
    {
        return base64_encode($s);

    }

    // end mybase64_encode()

    public static function getOrderType($order) {
        $payment_method = $order['payment_method'];
        $paycodes = preg_split('/,/', trim(str_replace('"','',KomfortkasseConfig::getConfig(KomfortkasseConfig::payment_methods, $order))));
        if (in_array($payment_method, $paycodes))
            return 'PREPAYMENT';
        $paycodes = preg_split('/,/', trim(str_replace('"','',KomfortkasseConfig::getConfig(KomfortkasseConfig::payment_methods_invoice, $order))));
        if (in_array($payment_method, $paycodes))
            return 'INVOICE';
        $paycodes = preg_split('/,/', trim(str_replace('"','',KomfortkasseConfig::getConfig(KomfortkasseConfig::payment_methods_cod, $order))));
        if (in_array($payment_method, $paycodes))
            return 'COD';
        return '';
    }

    public static function readinvoicepdf() {
        KomfortkasseOrder::getInvoicePdfPrepare();

        if (!KomfortkasseConfig::getConfig(KomfortkasseConfig::activate_export)) {
            return;
        }

        if (Komfortkasse::check() === false) {
            return;
        }

        $invoiceNumber = KomfortkasseConfig::getRequestParameter('o');
        $invoiceNumber = Komfortkasse::kkdecrypt($invoiceNumber);
        $orderNumber = KomfortkasseConfig::getRequestParameter('order_id');
        $orderNumber = Komfortkasse::kkdecrypt($orderNumber);

        return KomfortkasseOrder::getInvoicePdf($invoiceNumber, $orderNumber);

    }

    public static function readconfig()
    {
        $key = KomfortkasseConfig::getRequestParameter('confkey');
        if (strpos($key, 'ACCESSCODE') !== false)
            return null;
        if (strpos($key, 'KEY') !== false)
            return null;

        $storeid = KomfortkasseConfig::getRequestParameter('storeid');

        $order = null;
        if ($storeid)
            $order ['store_id'] = $storeid;

        KomfortkasseConfig::output(KomfortkasseConfig::getConfig($key, $order));
    }

}