<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData;

use Zend\GData;
use Zend\Http;

/**
 * @category   Zend
 * @package    Zend_GData
 * @subpackage UnitTests
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
