<?php

namespace ZendTest\I18n\Translator\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use Locale;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\Loader\Gettext as GettextLoader;

class GettextTest extends TestCase
{
    protected $testFilesDir;
    protected $originalLocale;

    public function setUp()
    {
        $this->originalLocale = Locale::getDefault();
        Locale::setDefault('en_EN');

        $this->testFilesDir = realpath(__DIR__ . '/../_files');
    }

    public function tearDown()
    {
        Locale::setDefault($this->originalLocale);
    }

    public function testLoaderFailsToLoadMissingFile()
    {
        $loader = new GettextLoader();
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException', 'Could not open file');
        $loader->load('missing', 'en_EN');
    }

    public function testLoaderFailsToLoadBadFile()
    {
        $loader = new GettextLoader();
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException',
                                    'is not a valid gettext file');
        $loader->load($this->testFilesDir . '/failed.mo', 'en_EN');
    }

    public function testLoaderLoadsEmptyFile()
    {
        $loader = new GettextLoader();
        $domain = $loader->load($this->testFilesDir . '/translation_empty.mo', 'en_EN');
        $this->assertInstanceOf('Zend\I18n\Translator\TextDomain', $domain);
    }

    public function testLoaderLoadsBigEndianFile()
    {
        $loader = new GettextLoader();
        $domain = $loader->load($this->testFilesDir . '/translation_bigendian.mo', 'en_EN');
        $this->assertInstanceOf('Zend\I18n\Translator\TextDomain', $domain);
    }

    public function testTranslatorAddsFile()
    {
        $translator = new Translator();
        $translator->addTranslationFile('gettext', $this->testFilesDir . '/translation_en.mo');

        $this->assertEquals('Message 1 (en)', $translator->translate('Message 1'));
        $this->assertEquals('Message 6', $translator->translate('Message 6'));
    }

    public function testTranslatorAddsFileToTextDomain()
    {
        $translator = new Translator();
        $translator->addTranslationFile('gettext', $this->testFilesDir . '/translation_en.mo', 'user');

        $this->assertEquals('Message 2 (en)', $translator->translate('Message 2', 'user'));
    }

    public function testTranslatorAddsPattern()
    {
        $translator = new Translator();
        $translator->addTranslationPattern(
            'gettext',
            $this->testFilesDir . '/testmo',
            'translation-%s.mo'
        );

        $this->assertEquals('Message 1 (en)', $translator->translate('Message 1', 'default', 'en_US'));
        $this->assertEquals('Nachricht 1', $translator->translate('Message 1', 'default', 'de_DE'));
    }

    public function testLoaderLoadsPluralRules()
    {
        $loader = new GettextLoader();
        $domain = $loader->load($this->testFilesDir . '/translation_en.mo', 'en_EN');

        $this->assertEquals(2, $domain->getPluralRule()->evaluate(0));
        $this->assertEquals(0, $domain->getPluralRule()->evaluate(1));
        $this->assertEquals(1, $domain->getPluralRule()->evaluate(2));
        $this->assertEquals(2, $domain->getPluralRule()->evaluate(10));
    }

    public function testTranslatorTranslatesPlurals()
    {
        $translator = new Translator();
        $translator->setLocale('en_EN');
        $translator->addTranslationFile(
            'gettext',
            $this->testFilesDir . '/translation_en.mo',
            'default',
            'en_EN'
        );

        $pl0 = $translator->translatePlural('Message 5', 'Message 5 Plural', 1);
        $pl1 = $translator->translatePlural('Message 5', 'Message 5 Plural', 2);
        $pl2 = $translator->translatePlural('Message 5', 'Message 5 Plural', 10);

        $this->assertEquals('Message 5 (en) Plural 0', $pl0);
        $this->assertEquals('Message 5 (en) Plural 1', $pl1);
        $this->assertEquals('Message 5 (en) Plural 2', $pl2);
    }
}
