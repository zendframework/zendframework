<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service_WindowsAzure
 */

namespace Zend\Service\WindowsAzure\RetryPolicy;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage RetryPolicy
 */
class NoRetry extends AbstractRetryPolicy
{
    /**
     * Execute function under retry policy
     *
     * @param string|array $function   Function to execute
     * @param array        $parameters Parameters for function call
     * @return mixed
     */
    public function execute($function, $parameters = array())
    {
        $returnValue = null;

        $returnValue = call_user_func_array($function, $parameters);
        return $returnValue;
    }
}
