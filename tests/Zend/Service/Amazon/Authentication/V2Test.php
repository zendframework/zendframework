<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Amazon\Authentication;

use Zend\Service\Amazon\Authentication;
use Zend\Service\Amazon\Authentication\Exception;

/**
 * Amazon V2 authentication test case
 *
 * @category   Zend
 * @package    Zend_Service_Amazon_Authentication
 * @subpackage UnitTests
 */
class V2Test extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Authentication\V2
     */
    private $_amazon;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->_amazon = new Authentication\V2('0PN5J17HBGZHT7JJ3X82', 'uV3F3YluFJax1cknvbcGwgjvx4QpvB+leU8dUj2o', '2009-07-15');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_amazon = null;
    }

    /**
     * Tests Authentication\V2::generateSignature()
     */
    public function testGenerateEc2PostSignature()
    {
        $url = "https://ec2.amazonaws.com/";
        $params = array();
        $params['Action'] = "DescribeImages";
        $params['ImageId.1'] = "ami-2bb65342";
        $params['Timestamp'] = "2009-11-11T13:52:38Z";

        $ret = $this->_amazon->generateSignature($url, $params);

        $this->assertEquals('8B2cxwK/dfezT49KEzD+wjo1ZbJCddyFOLA0RNZobbc=', $params['Signature']);
        $this->assertEquals(file_get_contents(dirname(__FILE__) . '/_files/ec2_v2_return.txt'), $ret);
    }

    public function testGenerateSqsGetSignature()
    {
        $url = "https://queue.amazonaws.com/770098461991/queue2";
        $params = array();
        $params['Action'] = "SetQueueAttributes";
        $params['Attribute.Name'] = "VisibilityTimeout";
        $params['Attribute.Value'] = "90";
        $params['Timestamp'] = "2009-11-11T13:52:38Z";

        $this->_amazon->setHttpMethod('GET');
        $ret = $this->_amazon->generateSignature($url, $params);

        $this->assertEquals('YSw7HXDqokM/A6DhLz8kG+sd+oD5eMjqx3a02A0+GkE=', $params['Signature']);
        $this->assertEquals(file_get_contents(dirname(__FILE__) . '/_files/sqs_v2_get_return.txt'), $ret);
    }

}

