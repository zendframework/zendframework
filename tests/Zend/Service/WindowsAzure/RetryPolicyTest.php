<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service_WindowsAzure
 */

namespace ZendTest\Service\WindowsAzure;

use Zend\Service\WindowsAzure\RetryPolicy\AbstractRetryPolicy;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
 */
class RetryPolicyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Helper variable for counting retries
     *
     * @var int
     */
    protected $_executedRetries = 0;

    /**
     * Helper variable for setting Exception count
     *
     * @var int
     */
    protected $_exceptionCount = 0;

    /**
     * Test retry policy - noRetry
     */
    public function testNoRetry()
    {
        $this->_executedRetries = 0;
        $policy = AbstractRetryPolicy::noRetry();
        $retries = $policy->execute(
            array($this, '_countRetries')
        );
        $this->assertEquals(1, $retries);
    }

    /**
     * Test retry policy - retryN
     */
    public function testRetryN()
    {
        $this->_executedRetries = 0;
        $this->_exceptionCount = 9;

        $policy = AbstractRetryPolicy::retryN(10, 100);
        $retries = $policy->execute(
            array($this, '_countRetriesAndThrowExceptions')
        );
        $this->assertEquals(10, $retries);
    }

    /**
     * Helper function, counting retries
     */
    public function _countRetries()
    {
        return ++$this->_executedRetries;
    }

    /**
     * Helper function, counting retries and generating number of exceptions
     */
    public function _countRetriesAndThrowExceptions()
    {
        ++$this->_executedRetries;
        if ($this->_exceptionCount-- > 0) {
            throw new \Exception("Exception thrown.");
        }
        return $this->_executedRetries;
    }
}
