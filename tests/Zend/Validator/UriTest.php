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

use Zend\Validator,
    ReflectionClass;

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

    /**
     * Data Provider for URIs, not necessarily complete
     *
     * @return array
     */
    public function uriDataProvider()
    {
        return array(
            //    Uri                                        relative? absolute?
            array('http',                                    true,     false),
            array('http:',                                   false,    false),
            array('http://',                                 false,    false),
            array('http:///',                                false,    true),
            array('http://www.example.org/',                 false,    true),
            array('http://www.example.org:80/',              false,    true),
            array('https://www.example.org/',                false,    true),
            array('https://www.example.org:80/',             false,    true),
            array('http://foo',                              false,    true),
            array('http://foo.local',                        false,    true),
            array('example.org',                             true,     false),
            array('example.org:',                            false,    false),
            array('ftp://user:pass@example.org/',            false,    true),
            array('http://example.org/?cat=5&test=joo',      false,    true),
            array('http://www.fi/?cat=5&amp;test=joo',       false,    true),
            array('http://[::1]/',                           false,    true),
            array('http://[2620:0:1cfe:face:b00c::3]/',      false,    true),
            array('http://[2620:0:1cfe:face:b00c::3]:80/',   false,    true),
            array('a:b',                                     false,    true),
            array('http://www.zend.com',                     false,    true),
            array('https://example.com:10082/foo/bar?query', false,    true),
            array('../relative/path',                        true,     false),
            array('?queryOnly',                              true,     false),
            array('#fragmentOnly',                           true,     false),
            array('mailto:bob@example.com',                  false,    true),
            array('bob@example.com',                         true,     false),
            array('http://a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com', false, true)
        );
    }

    /**
     * @dataProvider uriDataProvider
     */
    public function testDefaultSettingsValidation($uri, $isRelative, $isAbsolute)
    {
        $validator = $this->validator;
        $this->assertTrue($validator->getAllowAbsolute());
        $this->assertTrue($validator->getAllowRelative());

        $uriHandler = $validator->getUriHandler();
        $this->assertInstanceOf('Zend\Uri\Uri', $uriHandler);
        $this->assertEquals(($isRelative || $isAbsolute), $validator->isValid($uri));
    }

    /**
     * @dataProvider uriDataProvider
     */
    public function testIsAbsoluteOnlyValidation($uri, $isRelative, $isAbsolute)
    {
        $validator = $this->validator;
        $validator->setAllowAbsolute(true)->setAllowRelative(false);
        $this->assertTrue($validator->getAllowAbsolute());
        $this->assertFalse($validator->getAllowRelative());

        $uriHandler = $validator->getUriHandler();
        $this->assertInstanceOf('Zend\Uri\Uri', $uriHandler);
        $this->assertEquals($isAbsolute, $validator->isValid($uri));
    }

    /**
     * @dataProvider uriDataProvider
     */
    public function testIsRelativeOnlyValidation($uri, $isRelative, $isAbsolute)
    {
        $validator = $this->validator;
        $validator->setAllowAbsolute(false)->setAllowRelative(true);
        $this->assertFalse($validator->getAllowAbsolute());
        $this->assertTrue($validator->getAllowRelative());

        $uriHandler = $validator->getUriHandler();
        $this->assertInstanceOf('Zend\Uri\Uri', $uriHandler);
        $this->assertEquals($isRelative, $validator->isValid($uri));
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
        $reflection = new ReflectionClass($validator);

        if (!$reflection->hasProperty('_messageTemplates')) {
            return;
        }

        $property = $reflection->getProperty('_messageTemplates');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageTemplates')
        );
    }

    public function testEqualsMessageVariables()
    {
        $validator = $this->validator;
        $reflection = new ReflectionClass($validator);

        if (!$reflection->hasProperty('_messageVariables')) {
            return;
        }

        $property = $reflection->getProperty('_messageVariables');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageVariables')
        );
    }
}
