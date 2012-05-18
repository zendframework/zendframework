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
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Soap\Server;

use Zend\Soap\Client\Local as SoapClient,
    Zend\Soap\Server,
    Zend\Soap\Server\DocumentLiteralWrapper,
    ZendTest\Soap\TestAsset\MyCalculatorService;

class DocumentLiteralWrapperTest extends \PHPUnit_Framework_TestCase
{
    const WSDL = '/_files/calculator.wsdl';

    private $client;

    public function setUp()
    {
        ini_set("soap.wsdl_cache_enabled", 0);
    }

    /**
     * @runInSeparateProcess
     */
    public function testDelegate()
    {
        $server = new Server(__DIR__ . self::WSDL);
        $server->setObject(new DocumentLiteralWrapper(new MyCalculatorService));

        // The local client needs an abstraction for this pattern as well.
        // This is just a test so we use the messy way.
        $client = new SoapClient($server, __DIR__ . self::WSDL);
        $ret = $client->add(array('x' => 10, 'y' => 20));

        $this->assertInstanceOf('stdClass', $ret);
        $this->assertEquals(30, $ret->addResult);
    }
}
