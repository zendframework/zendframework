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
class RetryN extends AbstractRetryPolicy
{
    /**
     * Number of retries
     *
     * @var int
     */
    protected $_retryCount = 1;

    /**
     * Interval between retries (in milliseconds)
     *
     * @var int
     */
    protected $_retryInterval = 0;

    /**
     * Constructor
     *
     * @param int $count                    Number of retries
     * @param int $intervalBetweenRetries   Interval between retries (in milliseconds)
     */
    public function __construct($count = 1, $intervalBetweenRetries = 0)
    {
        $this->_retryCount    = $count;
        $this->_retryInterval = $intervalBetweenRetries;
    }

    /**
     * Execute function under retry policy
     *
     * @param string|array $function       Function to execute
     * @param array        $parameters     Parameters for function call
     * @throws Exception\ExcessiveRetrievesException
     * @return mixed
     */
    public function execute($function, $parameters = array())
    {
        $returnValue = null;

        for ($retriesLeft = $this->_retryCount; $retriesLeft >= 0; --$retriesLeft) {
            try {
                $returnValue = call_user_func_array($function, $parameters);
                return $returnValue;
            } catch (\Exception $ex) {
                if ($retriesLeft == 1) {
                    throw new Exception\ExcessiveRetrievesException(
                        'Exceeded retry count of ' . $this->_retryCount
                        . '. ' . $ex->getMessage()
                    );
                }

                usleep($this->_retryInterval * 1000);
            }
        }
    }
}
