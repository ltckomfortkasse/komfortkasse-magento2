<?php
namespace Ltc\Komfortkasse\Observer;
/**
 * Komfortkasse
 * Magento2 Plugin - Abstract Observer Class with getRegName()
 *
 * @version 1.4.0.1-Magento2 */
use Magento\Framework\Event\ObserverInterface;
abstract class AbstractRegObserver implements ObserverInterface
{
    protected function getRegName(\Magento\Framework\Event\Observer $observer)
    {
        $id = $observer->getOrder()->getIncrementId();
        if ($id) {
            $regName = 'komfortkasse_order_status_'.$id;
            return $regName;
        }
    
    }
}
