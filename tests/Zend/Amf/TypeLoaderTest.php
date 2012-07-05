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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Amf;

use Zend\Amf\Parser;

/**
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Amf
 */
class TypeloaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * test that we can get the server name from the client name for deserialization.
     *
     */
    public function testGetMappedClassNameForClient()
    {
        $class = Parser\TypeLoader::getMappedClassName('flex.messaging.messages.RemotingMessage');
        $this->assertEquals('Zend\\Amf\\Value\\Messaging\\RemotingMessage', $class);
    }

    /**
     * Test that we can get the return name from the server name for serialization
     *
     */
    public function testGetMappedClassNameForServer()
    {
        $class = Parser\TypeLoader::getMappedClassName('Zend\\Amf\\Value\\Messaging\\RemotingMessage');
        $this->assertEquals('flex.messaging.messages.RemotingMessage', $class);
    }

    /**
     * Test that we can find and load the remote matching class name
     *
     */
    public function testLoadTypeSuccess(){
        $class = Parser\TypeLoader::loadType('flex.messaging.messages.RemotingMessage');
        $this->assertEquals('Zend\\Amf\\Value\\Messaging\\RemotingMessage', $class);
    }

    /**
     * Test that adding our own mappping will result in it being added to the classMap
     *
     */
    public function testSetMappingClass()
    {
        Parser\TypeLoader::setMapping('com.example.vo.Contact','ZendTest\\Amf\\TestAsset\\Contact');
        $class = Parser\TypeLoader::getMappedClassName('com.example.vo.Contact');
        $this->assertEquals('ZendTest\\Amf\\TestAsset\\Contact', $class);
    }

    public function testUnknownClassMap() {
        $class = Parser\TypeLoader::loadType('com.example.vo.Bogus');
        $this->assertEquals('stdClass', $class);
    }
}
