<?php

namespace Ltc\Komfortkasse\Controller\Main;

class Index extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if (is_array($params) && sizeof($params) > 0 && $params ['action']) {
            $action = $params ['action'];
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $helper = $om->get('\Ltc\Komfortkasse\Helper\Komfortkasse');
            switch ($action) {
                case 'info' :
                    $helper->info();
                    break;
                case 'init' :
                    $helper->init();
                    break;
                case 'test' :
                    $helper->test();
                    break;
                case 'readorders' :
                    $helper->readorders();
                    break;
                case 'updateorders' :
                    $helper->updateorders();
                    break;
                case 'readrefunds' :
                    $helper->readrefunds();
                    break;
                case 'updaterefunds' :
                    $helper->updaterefunds();
                    break;
                case 'readinvoicepdf' :
                    $content = $helper->readinvoicepdf();
                    if (!$content)
                        return;
                    
                    $contentType = 'application/pdf';
                    $contentLength = strlen($content);
                    
                    $this->getResponse()->setHttpResponseCode(200)->setHeader('Pragma', 'public', true)->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)->setHeader('Content-type', $contentType, true)->setHeader('Content-Length', $contentLength, true)->setHeader('Content-Disposition', 'attachment; filename="invoice.pdf"', true)->setHeader('Last-Modified', date('r'), true);
                    $this->getResponse()->setBody($content);
                    
                    break;
                default :
                    echo "Error: Unknwon action: $action";
            }
        }
    
    }
}