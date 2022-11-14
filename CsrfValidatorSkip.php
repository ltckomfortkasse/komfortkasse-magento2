<?php

namespace Ltc\Komfortkasse;

/**
 * disable CSRF checks for komfortkasse calls
 */
class CsrfValidatorSkip
{

    /**
     *
     * @param \Magento\Framework\App\Request\CsrfValidator $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\ActionInterface $action
     */
    public function aroundValidate($subject, \Closure $proceed, $request, $action)
    {
        if ($request->getModuleName() == 'komfortkasse') {
            return; // Skip CSRF check
        }
        $proceed($request, $action);
        // Proceed Magento 2 core functionalities
    }
}