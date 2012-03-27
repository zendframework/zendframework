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
 * @package    Zend_GData
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData;
use Zend\GData;
use Zend\Http;

/**
 * @category   Zend
 * @package    Zend_GData
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 */
class GDataTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaultHttpClient()
    {
        $gdata = new GData\GData();
        $client = $gdata->getHttpClient();
        $this->assertTrue($client instanceof Http\Client,
            'Expecting object of type Zend_Http_Client, got '
            . (gettype($client) == 'object' ? get_class($client) : gettype($client))
        );
    }

    public function testSpecificHttpClient()
    {
        $client = new Http\Client();
        $gdata = new GData\GData($client);
        $client2 = $gdata->getHttpClient();
        $this->assertTrue($client2 instanceof Http\Client,
            'Expecting object of type Zend_Http_Client, got '
            . (gettype($client) == 'object' ? get_class($client) : gettype($client))
        );
        $this->assertSame($client, $client2);
    }

    public function testExceptionNotHttpClient()
    {
        $obj = new \ArrayObject();
        try {
            $gdata = new GData\GData($obj);
            $this->fail('Expecting to catch Zend_GData_App_HttpException');
        } catch (\Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend\GData\App\HttpException'),
                'Expecting Zend\GData\App\HttpException, got '.get_class($e));
            $this->assertEquals('Argument is not an instance of Zend\Http\Client.', $e->getMessage());
        }
    }

    public function testGetFeedExceptionInvalidLocationType()
    {
        $gdata = new GData\GData();
        try {
            // give it neither a string nor a Zend_GData_Query object,
            // and see if it throws an exception.
            $feed = $gdata->getFeed(new \stdClass());
            $this->fail('Expecting to catch Zend\GData\App\InvalidArgumentException');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Zend\GData\App\InvalidArgumentException', $e,
                'Expecting Zend\GData\App\InvalidArgumentException, got '.get_class($e));
            $this->assertEquals('You must specify the location as either a string URI or a child of Zend\GData\Query', $e->getMessage());
        }
    }

    public function testGetEntryExceptionInvalidLocationType()
    {
        $gdata = new GData\GData();
        try {
            // give it neither a string nor a Zend_GData_Query object,
            // and see if it throws an exception.
            $feed = $gdata->getEntry(new \stdClass());
            $this->fail('Expecting to catch Zend\GData\App\InvalidArgumentException');
        } catch (\Exception $e) {
            $this->assertInstanceOf('Zend\GData\App\InvalidArgumentException', $e,
                'Expecting Zend\GData\App\InvalidArgumentException, got '.get_class($e));
            $this->assertEquals('You must specify the location as either a string URI or a child of Zend\GData\Query', $e->getMessage());
        }
    }

}
