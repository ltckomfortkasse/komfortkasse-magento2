<?php

namespace Ltc\Komfortkasse\Observer;

/**
 * Komfortkasse
 * Magento2 Plugin - Observer Class for registering order status at load
 *
 * @version 1.9.8-Magento2
 */
class CheckOrderStatus extends AbstractRegObserver
{

    /**
     *
     * {@inheritdoc}
     * @see \Magento\Framework\Event\ObserverInterface::execute()
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $registry = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\Registry');
        $regName = $this->getRegName($observer);
        $orderStatus = $registry->registry($regName);
        if ($regName && $orderStatus) {
            if ($orderStatus != $observer->getOrder()->getStatus()) {
                $om = \Magento\Framework\App\ObjectManager::getInstance();
                $helper = $om->get('\Ltc\Komfortkasse\Helper\Komfortkasse');
                // runs inside the order save transaction: the notification must never
                // abort (and roll back) the order save
                try {
                    $helper->notifyorder($observer->getOrder()->getIncrementId());
                } catch (\Throwable $e) {
                }
            }

            $registry->unregister($regName);
        }
    }
}
