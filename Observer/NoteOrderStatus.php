<?php

namespace Ltc\Komfortkasse\Observer;

/**
 * Komfortkasse
 * Magento2 Plugin - Observer Class for check order status at save (and notifying Komfortkasse)
 *
 * @version 1.4.0.1-Magento2
 */
class NoteOrderStatus extends AbstractRegObserver
{


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $registry = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\Registry');
        $regName = $this->getRegName($observer);
        if ($regName && !$registry->registry($regName)) {
            $registry->register($regName, $observer->getOrder()->getStatus());
        }
    
    }
}