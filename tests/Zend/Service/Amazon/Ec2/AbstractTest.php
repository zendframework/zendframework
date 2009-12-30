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
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AbstractTest.php 17667 2009-08-18 21:40:09Z mikaelkael $
 */

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Service/Amazon/Ec2/Abstract.php';

/**
 * @todo: Rename class to Zend_Service_Amazon_AbstractTest
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_Ec2
 */
class Zend_Service_Amazon_Ec2_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testNoKeysThrowException()
    {
        try {
            $class = new TestAmamzonAbstract();
            $this->fail('Exception should be thrown when no keys are passed in.');
        } catch(Zend_Service_Amazon_Exception $zsae) {}
    }

    public function testSetRegion()
    {
        TestAmamzonAbstract::setRegion('eu-west-1');

        $class = new TestAmamzonAbstract('TestAccessKey', 'TestSecretKey');
        $this->assertEquals('eu-west-1', $class->returnRegion());
    }

    public function testSetInvalidRegionThrowsException()
    {
        try {
            TestAmamzonAbstract::setRegion('eu-west-1a');
            $this->fail('Invalid Region Set with no Exception Thrown');
        } catch (Zend_Service_Amazon_Exception $zsae) {
            // do nothing
        }
    }
    
    public function testSignParamsWithSpaceEncodesWithPercentInsteadOfPlus()
    {
        $class = new TestAmamzonAbstract('TestAccessKey', 'TestSecretKey');
        $ret = $class->testSign(array('Action' => 'Space Test'));
        
        // this is the encode signuature with urlencode - It's Invalid!
        $invalidSignature = 'EeHAfo7cMcLyvH4SW4fEpjo51xJJ4ES1gdjRPxZTlto=';
        
        $this->assertNotEquals($ret, $invalidSignature);
    }
}

class TestAmamzonAbstract extends Zend_Service_Amazon_Ec2_Abstract
{

    public function returnRegion()
    {
        return $this->_region;
    }
    
    public function testSign($params)
    {
        return $this->signParameters($params);
    }
}

