<?php

namespace Ltc\Komfortkasse\Observer;

/**
 * Komfortkasse
 * Magento2 Plugin - Observer Class for registering order status at load
 *
 * @version 1.4.0.1-Magento2
 */
class CheckOrderStatus extends AbstractRegObserver
{


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $registry = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\Registry');
        $regName = $this->getRegName($observer);
        $orderStatus = $registry->registry($regName);
        if ($regName && $orderStatus) {
            if ($orderStatus != $observer->getOrder()->getStatus()) {
                $helper = \Magento\Framework\App\ObjectManager::getInstance()->get('\Ltc\Komfortkasse\Helper\Komfortkasse');
                $helper->notifyorder($observer->getOrder()->getIncrementId());
            }
            
            $registry->unregister($regName);
        }
    
    }
}