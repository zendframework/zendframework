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
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @group      Zend_Application
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Resource_SessionTest extends PHPUnit_Framework_TestCase
{
    public $resource;

    public function setUp()
    {
        $this->resource = new Zend_Application_Resource_Session();
    }

    public function testSetSaveHandler()
    {
        $saveHandler = $this->getMock('Zend_Session_SaveHandler_Interface');

        $this->resource->setSaveHandler($saveHandler);
        $this->assertSame($saveHandler, $this->resource->getSaveHandler());
    }

    public function testSetSaveHandlerString()
    {
        $saveHandlerClassName = 'Zend_Application_Resource_SessionTestHandlerMock1';
        $saveHandler = $this->getMock('Zend_Session_SaveHandler_Interface', array(), array(), $saveHandlerClassName);

        $this->resource->setSaveHandler($saveHandlerClassName);

        $this->assertType($saveHandlerClassName, $this->resource->getSaveHandler());
    }

    public function testSetSaveHandlerArray()
    {
        $saveHandlerClassName = 'Zend_Application_Resource_SessionTestHandlerMock2';
        $saveHandler = $this->getMock('Zend_Session_SaveHandler_Interface', array(), array(), $saveHandlerClassName);

        $this->resource->setSaveHandler(array('class' => $saveHandlerClassName));

        $this->assertType($saveHandlerClassName, $this->resource->getSaveHandler());
    }

    public function testSetOptions()
    {
        Zend_Session::setOptions(array(
            'use_only_cookies' => false,
            'remember_me_seconds' => 3600,
        ));

        $this->resource->setOptions(array(
             'use_only_cookies' => true,
             'remember_me_seconds' => 7200,
        ));

        $this->resource->init();

        $this->assertEquals(1, Zend_Session::getOptions('use_only_cookies'));
        $this->assertEquals(7200, Zend_Session::getOptions('remember_me_seconds'));
    }

    public function testInitSetsSaveHandler()
    {
        $saveHandler = $this->getMock('Zend_Session_SaveHandler_Interface');

        $this->resource->setSaveHandler($saveHandler);

        $this->resource->init();

        $this->assertSame($saveHandler, Zend_Session::getSaveHandler());
    }
}
