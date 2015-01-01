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
use Zend\I18n\Translator\Loader\PhpArray as PhpArrayLoader;

class PhpArrayTest extends TestCase
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
        Locale::setDefault('en_EN');

        $this->testFilesDir = realpath(__DIR__ . '/../_files');

        $this->originalIncludePath = get_include_path();
        set_include_path($this->testFilesDir . PATH_SEPARATOR . $this->testFilesDir . '/translations.phar');
    }

    public function tearDown()
    {
        if (extension_loaded('intl')) {
            Locale::setDefault($this->originalLocale);
            set_include_path($this->originalIncludePath);
        }
    }

    public function testLoaderFailsToLoadMissingFile()
    {
        $loader = new PhpArrayLoader();
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException', 'Could not find or open file');
        $loader->load('en_EN', 'missing');
    }

    public function testLoaderFailsToLoadNonArray()
    {
        $loader = new PhpArrayLoader();
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException',
                                    'Expected an array, but received');
        $loader->load('en_EN', $this->testFilesDir . '/failed.php');
    }

    public function testLoaderLoadsEmptyArray()
    {
        $loader = new PhpArrayLoader();
        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_empty.php');
        $this->assertInstanceOf('Zend\I18n\Translator\TextDomain', $textDomain);
    }

    public function testLoaderReturnsValidTextDomain()
    {
        $loader = new PhpArrayLoader();
        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_en.php');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderLoadsPluralRules()
    {
        $loader     = new PhpArrayLoader();
        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_en.php');

        $this->assertEquals(2, $textDomain->getPluralRule()->evaluate(0));
        $this->assertEquals(0, $textDomain->getPluralRule()->evaluate(1));
        $this->assertEquals(1, $textDomain->getPluralRule()->evaluate(2));
        $this->assertEquals(2, $textDomain->getPluralRule()->evaluate(10));
    }

    public function testLoaderLoadsFromIncludePath()
    {
        $loader = new PhpArrayLoader();
        $loader->setUseIncludePath(true);
        $textDomain = $loader->load('en_EN', 'translation_en.php');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderLoadsFromPhar()
    {
        $loader = new PhpArrayLoader();
        $loader->setUseIncludePath(true);
        $textDomain = $loader->load('en_EN', 'phar://' . $this->testFilesDir . '/translations.phar/translation_en.php');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }
}
