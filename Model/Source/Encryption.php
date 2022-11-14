<?php

namespace Ltc\Komfortkasse\Model\Source;

class Encryption implements \Magento\Framework\Option\ArrayInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Magento\Framework\Data\OptionSourceInterface::toOptionArray()
     */
    public function toOptionArray()
    {
        return [ [ 'value' => "openssl",'label' => __('OpenSSL Encryption (asynchronous)')
        ],[ 'value' => "mcrypt",'label' => __('MCrypt Encryption (synchronous)')
        ],[ 'value' => "base64",'label' => __('Base64 Encoding')
        ]
        ];
    }
}
