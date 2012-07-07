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
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator;
use Zend\Uri\Exception\InvalidArgumentException;

/**
 * Test helper
 */

/**
 * @see \Zend\Validator\Uri
 */

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class UriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Validator\Uri
     */
    protected $validator;

    /**
     * Creates a new Uri Validator object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->validator = new Validator\Uri();
    }

    public function testHasDefaultSettingsAndLazyLoadsUriHandler()
    {
        $validator = $this->validator;
        $uriHandler = $validator->getUriHandler();
        $this->assertInstanceOf('Zend\Uri\Uri', $uriHandler);
        $this->assertTrue($validator->getAllowRelative());
        $this->assertTrue($validator->getAllowAbsolute());
    }

    public function testConstructorWithArraySetsOptions()
    {
        $uriMock = $this->getMock('Zend\Uri\Uri');
        $validator = new Validator\Uri(array(
            'uriHandler' => $uriMock,
            'allowRelative' => false,
            'allowAbsolute' => false,
        ));
        $this->assertEquals($uriMock, $validator->getUriHandler());
        $this->assertFalse($validator->getAllowRelative());
        $this->assertFalse($validator->getAllowAbsolute());
    }

    public function testConstructorWithArgsSetsOptions()
    {
        $uriMock = $this->getMock('Zend\Uri\Uri');
        $validator = new Validator\Uri($uriMock, false, false);
        $this->assertEquals($uriMock, $validator->getUriHandler());
        $this->assertFalse($validator->getAllowRelative());
        $this->assertFalse($validator->getAllowAbsolute());
    }

    public function allowOptionsDataProvider()
    {
        return array(
            //    allowAbsolute allowRelative isAbsolute isRelative isValid expects
            array(true,         true,         true,      false,     true,   true),
            array(true,         true,         false,     true,      true,   true),
            array(false,        true,         true,      false,     true,   false),
            array(false,        true,         false,     true,      true,   true),
            array(true,         false,        true,      false,     true,   true),
            array(true,         false,        false,     true,      true,   false),
            array(false,        false,        true,      false,     true,   false),
            array(false,        false,        false,     true,      true,   false),
            array(true,         true,         false,     false,     false,  false),
        );
    }

    /**
     * @dataProvider allowOptionsDataProvider
     */
    public function testUriHandlerBehaviorWithAllowSettings(
        $allowAbsolute, $allowRelative, $isAbsolute, $isRelative, $isValid, $expects
    ) {
        $uriMock = $this->getMock(
            'Zend\Uri\Uri',
            array('parse', 'isValid', 'isAbsolute', 'isValidRelative')
        );
        $uriMock->expects($this->once())
            ->method('isValid')->will($this->returnValue($isValid));
        $uriMock->expects($this->any())
            ->method('isAbsolute')->will($this->returnValue($isAbsolute));
        $uriMock->expects($this->any())
            ->method('isValidRelative')->will($this->returnValue($isRelative));

        $this->validator->setUriHandler($uriMock)
            ->setAllowAbsolute($allowAbsolute)
            ->setAllowRelative($allowRelative);

        $this->assertEquals($expects, $this->validator->isValid('uri'));
    }

    public function testUriHandlerThrowsExceptionInParseMethodNotValid()
    {
        $uriMock = $this->getMock('Zend\Uri\Uri');
        $uriMock->expects($this->once())
            ->method('parse')
            ->will($this->throwException(new InvalidArgumentException()));

        $this->validator->setUriHandler($uriMock);
        $this->assertFalse($this->validator->isValid('uri'));
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->validator->getMessages());
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertObjectHasAttribute('messageTemplates', $validator);
        $this->assertAttributeEquals($validator->getOption('messageTemplates'), 'messageTemplates', $validator);
    }
}
