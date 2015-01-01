<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\I18n\Translator\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use Locale;
use Zend\I18n\Translator\Loader\PhpMemoryArray as PhpMemoryArrayLoader;

class PhpMemoryArrayTest extends TestCase
{
    protected $testFilesDir;
    protected $originalLocale;
    protected $originalIncludePath;

    public function setUp()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->originalLocale = Locale::getDefault();
        Locale::setDefault('en_US');

        $this->testFilesDir = realpath(__DIR__ . '/../_files/phpmemoryarray');
    }

    public function tearDown()
    {
        if (extension_loaded('intl')) {
            Locale::setDefault($this->originalLocale);
        }
    }
    public function testLoaderFailsToLoadNonArray()
    {
        $loader = new PhpMemoryArrayLoader('foo');
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException',
                                    'Expected an array, but received');
        $loader->load('en_US', 'default');
    }

    public function testLoaderFailsToLoadMissingTextDomain()
    {
        $loader = new PhpMemoryArrayLoader(array());
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException',
                                    'Expected textdomain "default" to be an array, but it is not set');
        $loader->load('en_US', 'default');
    }

    public function testLoaderFailsToLoadNonArrayLocale()
    {
        $loader = new PhpMemoryArrayLoader(array('default' => array()));
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException',
                                    'Expected locale "en_US" to be an array, but it is not set');
        $loader->load('en_US', 'default');
    }

    public function testLoaderLoadsEmptyArray()
    {
        $loader = new PhpMemoryArrayLoader(include $this->testFilesDir . '/translation_empty.php');
        $textDomain = $loader->load('en_US', 'default');
        $this->assertInstanceOf('Zend\I18n\Translator\TextDomain', $textDomain);
    }

    public function testLoaderReturnsValidTextDomain()
    {
        $loader = new PhpMemoryArrayLoader(include $this->testFilesDir . '/translation_en.php');
        $textDomain = $loader->load('en_US', 'default');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderLoadsPluralRules()
    {
        $loader     = new PhpMemoryArrayLoader(include $this->testFilesDir . '/translation_en.php');
        $textDomain = $loader->load('en_US', 'default');

        $this->assertEquals(2, $textDomain->getPluralRule()->evaluate(0));
        $this->assertEquals(0, $textDomain->getPluralRule()->evaluate(1));
        $this->assertEquals(1, $textDomain->getPluralRule()->evaluate(2));
        $this->assertEquals(2, $textDomain->getPluralRule()->evaluate(10));
    }
}
