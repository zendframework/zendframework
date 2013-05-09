<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Uri
 */

namespace ZendTest\Uri;

use Zend\Uri\UriFactory;

/**
 * @category   Zend
 * @package    Zend_Uri
 * @subpackage UnitTests
 * @group      Zend_Uri
 */
class UriFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * General composing / parsing tests
     */

    /**
     * Test registering a new Scheme
     *
     * @param        string $scheme
     * @param        string $class
     * @dataProvider registeringNewSchemeProvider
     */
    public function testRegisteringNewScheme($scheme, $class)
    {
        $this->assertAttributeNotContains($class, 'schemeClasses', '\Zend\Uri\UriFactory');
        UriFactory::registerScheme($scheme, $class);
        $this->assertAttributeContains($class, 'schemeClasses', '\Zend\Uri\UriFactory');
        UriFactory::unregisterScheme($scheme);
        $this->assertAttributeNotContains($class, 'schemeClasses', '\Zend\Uri\UriFactory');

    }

    /**
     * Provide the data for the RegisterNewScheme-test
     */
    public function registeringNewSchemeProvider()
    {
        return array(
            array('ssh', 'Foo\Bar\Class'),
            array('ntp', 'No real class at all!!!'),
        );
    }

    /**
     * Test creation of new URI with an existing scheme-classd
     *
     * @param string $uri           THe URI to create
     * @param string $expectedClass The class expected
     *
     * @dataProvider createUriWithFactoryProvider
     */
    public function testCreateUriWithFactory($uri, $expectedClass)
    {
        $class = UriFactory::factory($uri);
        $this->assertInstanceof($expectedClass, $class);
    }

    /**
     * Providethe data for the CreateUriWithFactory-test
     *
     * @return array
     */
    public function createUriWithFactoryProvider()
    {
        return array(
            array('http://example.com', 'Zend\Uri\Http'),
            array('https://example.com', 'Zend\Uri\Http'),
            array('mailto://example.com', 'Zend\Uri\Mailto'),
            array('file://example.com', 'Zend\Uri\File'),
        );
    }

    /**
     * Test, that unknown Schemes will result in an exception
     *
     * @param string $uri an uri with an unknown scheme
     * @expectedException Zend\Uri\Exception\InvalidArgumentException
     * @dataProvider unknownSchemeThrowsExceptionProvider
     */
    public function testUnknownSchemeThrowsException($uri)
    {
        $url = UriFactory::factory($uri);
    }

    /**
     * Provide data to the unknownSchemeThrowsException-TEst
     *
     * @return array
     */
    public function unknownSchemeThrowsExceptionProvider()
    {
        return array(
            array('foo://bar'),
            array('ssh://bar'),
        );
    }
}
