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
 * @package    Zend_Service_Amazon_Authentication
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 11973 2008-10-15 16:00:56Z matthew $
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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

