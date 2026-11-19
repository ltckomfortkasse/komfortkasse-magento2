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

    /**
     *
     * {@inheritdoc}
     * @see \Magento\Framework\Event\ObserverInterface::execute()
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $regName = $this->getRegName($observer);
        if ($regName) {
            $registry = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\Registry');
            // the order may already have been loaded in this request (NoteOrderStatus)
            if ($registry->registry($regName) !== null) {
                $registry->unregister($regName);
            }
            $registry->register($regName, '_new');
        }
    }
}
