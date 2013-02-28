<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\I18n\Translator\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use Locale;
use Zend\I18n\Translator\Loader\Ini as IniLoader;

class IniTest extends TestCase
{
    protected $testFilesDir;
    protected $originalLocale;

    public function setUp()
    {
        $this->testFilesDir = realpath(__DIR__ . '/../_files');
    }

    public function testLoaderFailsToLoadMissingFile()
    {
        $loader = new IniLoader();
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException', 'Could not open file');
        $loader->load('en_EN', 'missing');
    }

    public function testLoaderLoadsEmptyFile()
    {
        $loader = new IniLoader();
        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_empty.ini');
        $this->assertInstanceOf('Zend\I18n\Translator\TextDomain', $textDomain);
    }

    public function testLoaderFailsToLoadNonArray()
    {
        $loader = new IniLoader();
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException',
                                    'Each INI row must be an array with message and translation');
        $loader->load('en_EN', $this->testFilesDir . '/failed.ini');
    }

    public function testLoaderFailsToLoadBadSyntax()
    {
        $loader = new IniLoader();
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException',
                                    'Each INI row must be an array with message and translation');
        $loader->load('en_EN', $this->testFilesDir . '/failed_syntax.ini');
    }

    public function testLoaderReturnsValidTextDomain()
    {
        $loader = new IniLoader();
        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_en.ini');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderReturnsValidTextDomainWithFileWithoutPlural()
    {
        $loader = new IniLoader();
        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_en_without_plural.ini');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderReturnsValidTextDomainWithSimpleSyntax()
    {
        $loader = new IniLoader();
        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_en_simple_syntax.ini');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderLoadsPluralRules()
    {
        $loader     = new IniLoader();
        $textDomain = $loader->load('en_EN', $this->testFilesDir . '/translation_en.ini');

        $this->assertEquals(2, $textDomain->getPluralRule()->evaluate(0));
        $this->assertEquals(0, $textDomain->getPluralRule()->evaluate(1));
        $this->assertEquals(1, $textDomain->getPluralRule()->evaluate(2));
        $this->assertEquals(2, $textDomain->getPluralRule()->evaluate(10));
    }
}
