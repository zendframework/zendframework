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
abstract class AbstractRetryPolicy
{
    /**
     * Execute function under retry policy
     *
     * @param string|array $function       Function to execute
     * @param array        $parameters     Parameters for function call
     * @return mixed
     */
    public abstract function execute($function, $parameters = array());

    /**
     * Create a NoRetry instance
     *
     * @return NoRetry
     */
    public static function noRetry()
    {
        return new NoRetry();
    }

    /**
     * Create a RetryN instance
     *
     * @param int $count                    Number of retries
     * @param int $intervalBetweenRetries   Interval between retries (in milliseconds)
     * @return RetryN
     */
    public static function retryN($count = 1, $intervalBetweenRetries = 0)
    {
        return new RetryN($count, $intervalBetweenRetries);
    }
}
