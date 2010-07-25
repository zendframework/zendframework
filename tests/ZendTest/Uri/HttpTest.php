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
 * @package    Zend_URI
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/**
 * @namespace
 */
namespace ZendTest\URI;
use Zend\URI\HTTP as HTTPUri;

/**
 * @category   Zend
 * @package    Zend_URI
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_URI
 * @group      Zend_URI_HTTP
 */
class HTTPTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that specific schemes are valid for this class
     * 
     * @param string $scheme
     * @dataProvider validSchemeProvider
     */
    public function testValidScheme($scheme)
    {
        $uri = new HTTPUri;
        $uri->setScheme($scheme);
        $this->assertEquals($scheme, $uri->getScheme());
    }
    
    /**
     * Test that specific schemes are invalid for this class
     * 
     * @param string $scheme
     * @dataProvider invalidSchemeProvider
     * @expectedException \Zend\URI\InvalidSchemeException
     */
    public function testInvalidScheme($scheme)
    {
        $uri = new HTTPUri;
        $uri->setScheme($scheme);
    }

    /**
     * Test that validateScheme returns false for schemes not valid for use
     * with the HTTP class
     * 
     * @param string $scheme
     * @dataProvider invalidSchemeProvider
     */
    public function testValidateSchemeInvalid($scheme)
    {
        $this->assertFalse(HTTPUri::validateScheme($scheme));
    }
    
    /**
     * Data Providers
     */
    
    /**
     * Valid HTTP schemes
     * 
     * @return array
     */
    static public function validSchemeProvider()
    {
        return array(
            array('http'),
            array('https'),
            array('HTTP'),
            array('Https'),
        );
    }
    
    /**
     * Invalid HTTP schemes
     * 
     * @return array
     */
    static public function invalidSchemeProvider()
    {
        return array(
            array('file'),
            array('mailto'),
            array('g'),
            array('http:')
        );
    }
}

