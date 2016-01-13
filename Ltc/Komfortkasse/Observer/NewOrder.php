<?php

namespace Ltc\Komfortkasse\Observer;

/**
 * Komfortkasse
 * Magento2 Plugin - Observer Class for new order
 *
 * @version 1.4.0.1-Magento2
 */
class NewOrder extends AbstractRegObserver
{


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $regName = $this->getRegName($observer);
        if ($regName) {
            $registry = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\Registry');
            $registry->register($regName, '_new');
        }
    
    }
}