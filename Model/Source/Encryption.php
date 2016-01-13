<?php
namespace Ltc\Komfortkasse\Model\Source;

class Encryption implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return array (array ('value' => "openssl",'label' => __('OpenSSL Encryption (asynchronous)') 
        ),array ('value' => "mcrypt",'label' => __('MCrypt Encryption (synchronous)') 
        ),array ('value' => "base64",'label' => __('Base64 Encoding') 
        ) 
        );
    
    }
}
