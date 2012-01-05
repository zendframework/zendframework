<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Service_WindowsAzure_RetryPolicy_AbstractRetryPolicy
 */

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_WindowsAzure
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_WindowsAzure_RetryPolicyTest extends PHPUnit_Framework_TestCase
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
        $policy = Zend_Service_WindowsAzure_RetryPolicy_AbstractRetryPolicy::noRetry();
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
        
        $policy = Zend_Service_WindowsAzure_RetryPolicy_AbstractRetryPolicy::retryN(10, 100);
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
            throw new Exception("Exception thrown.");
        }
        return $this->_executedRetries;
    }
}
