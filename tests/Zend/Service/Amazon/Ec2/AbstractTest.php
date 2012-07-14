<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Amazon\Ec2;

use Zend\Service\Amazon;
use Zend\Service\Amazon\Ec2\Exception;

/**
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_Ec2
 */
class AbstractTest extends \PHPUnit_Framework_TestCase
{
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
