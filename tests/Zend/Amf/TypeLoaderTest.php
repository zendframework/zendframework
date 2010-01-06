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
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Amf_TypeloaderTest::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';
require_once 'Zend/Amf/Parse/TypeLoader.php';

/**
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Amf
 */
class Zend_Amf_TypeloaderTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Amf_ResponseTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * test that we can get the server name from the client name for deserialization.
     *
     */
    public function testGetMappedClassNameForClient()
    {
        $class = Zend_Amf_Parse_TypeLoader::getMappedClassName('flex.messaging.messages.RemotingMessage');
        $this->assertEquals('Zend_Amf_Value_Messaging_RemotingMessage', $class);
    }

    /**
     * Test that we can get the return name from the server name for serialization
     *
     */
    public function testGetMappedClassNameForServer()
    {
        $class = Zend_Amf_Parse_TypeLoader::getMappedClassName('Zend_Amf_Value_Messaging_RemotingMessage');
        $this->assertEquals('flex.messaging.messages.RemotingMessage', $class);
    }

    /**
     * Test that we can find and load the remote matching class name
     *
     */
    public function testLoadTypeSuccess(){
        $class = Zend_Amf_Parse_TypeLoader::loadType('flex.messaging.messages.RemotingMessage');
        $this->assertEquals('Zend_Amf_Value_Messaging_RemotingMessage', $class);
    }

    /**
     * Test that adding our own mappping will result in it being added to the classMap
     *
     */
    public function testSetMappingClass()
    {
        Zend_Amf_Parse_TypeLoader::setMapping('com.example.vo.Contact','Contact');
        $class = Zend_Amf_Parse_TypeLoader::getMappedClassName('com.example.vo.Contact');
        $this->assertEquals('Contact', $class);
    }

    public function testUnknownClassMap() {
        $class = Zend_Amf_Parse_TypeLoader::loadType('com.example.vo.Bogus');
        $this->assertEquals('stdClass', $class);
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Amf_TypeloaderTest::main') {
    Zend_Amf_ResponseTest::main();
}

