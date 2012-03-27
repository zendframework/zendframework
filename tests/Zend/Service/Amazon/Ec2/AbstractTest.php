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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\Amazon\Ec2;
use Zend\Service\Amazon,
    Zend\Service\Amazon\Ec2\Exception;

/**
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_Ec2
 */
class AbstractTest extends \PHPUnit_Framework_TestCase
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
        Amazon\AbstractAmazon::setKeys(null, null); // to make sure there's NO DEFAULTS
        $this->setExpectedException(
            'Zend\Service\Amazon\Exception\InvalidArgumentException',
            'AWS keys were not supplied');
        $class = new TestAmazonAbstract();
    }

    public function testSetRegion()
    {
        TestAmazonAbstract::setRegion('eu-west-1');

        $class = new TestAmazonAbstract('TestAccessKey', 'TestSecretKey');
        $this->assertEquals('eu-west-1', $class->returnRegion());
    }

    public function testSetInvalidRegionThrowsException()
    {
        $this->setExpectedException(
            'Zend\Service\Amazon\Ec2\Exception\InvalidArgumentException',
            'Invalid Amazon Ec2 Region');
        TestAmazonAbstract::setRegion('eu-west-1a');
    }
    
    public function testSignParamsWithSpaceEncodesWithPercentInsteadOfPlus()
    {
        $class = new TestAmazonAbstract('TestAccessKey', 'TestSecretKey');
        $ret = $class->testSign(array('Action' => 'Space Test'));
        
        // this is the encode signuature with urlencode - It's Invalid!
        $invalidSignature = 'EeHAfo7cMcLyvH4SW4fEpjo51xJJ4ES1gdjRPxZTlto=';
        
        $this->assertNotEquals($ret, $invalidSignature);
    }
}

class TestAmazonAbstract extends \Zend\Service\Amazon\Ec2\AbstractEc2
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

