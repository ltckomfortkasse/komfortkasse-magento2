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

            // load other needed classes because include_once is not allowed
            $om->get('\Ltc\Komfortkasse\Helper\KomfortkasseConfig');
            $om->get('\Ltc\Komfortkasse\Helper\KomfortkasseOrder');

            switch ($action) {
                case 'info' :
                    $this->getResponse()->setBody($helper->info());
                    break;
                case 'init' :
                    $this->getResponse()->setBody($helper->init());
                    break;
                case 'test' :
                    $this->getResponse()->setBody($helper->test());
                    break;
                case 'readorders' :
                    $this->getResponse()->setBody($helper->readorders());
                    break;
                case 'updateorders' :
                    $this->getResponse()->setBody($helper->updateorders());
                    break;
                case 'readrefunds' :
                    $this->getResponse()->setBody($helper->readrefunds());
                    break;
                case 'updaterefunds' :
                    $this->getResponse()->setBody($helper->updaterefunds());
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
                case 'readconfig' :
                    $this->getResponse()->setBody($helper->readconfig());
                    break;
                default :
                    $this->getResponse()->setBody("Error: Unknwon action: $action");
            }
        }

    }
}