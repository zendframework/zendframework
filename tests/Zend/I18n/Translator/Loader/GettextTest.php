<?php

namespace ZendTest\I18n\Translator\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use Locale;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\Loader\Gettext as GettextLoader;

class GettextTest extends TestCase
{
    /**
     * @var Translator
     */
    protected $translator;
    protected $testFilesDir;
    protected $originalLocale;

    public function setUp()
    {
        $this->originalLocale = Locale::getDefault();
        $this->translator     = new Translator();

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
        $textDomain = $loader->load($this->testFilesDir . '/translation_empty.mo', 'en_EN');
        $this->assertInstanceOf('Zend\I18n\Translator\TextDomain', $textDomain);
    }

    public function testTranslatorAddsFile()
    {
        $this->translator->addTranslationFile('gettext', $this->testFilesDir . '/translation_en.mo');

        $this->assertEquals('Message 1 (en)', $this->translator->translate('Message 1'));
        $this->assertEquals('Message 6', $this->translator->translate('Message 6'));
    }

    public function testTranslatorAddsFileToTextDomain()
    {
        $this->translator->addTranslationFile('gettext', $this->testFilesDir . '/translation_en.mo', 'user');

        $this->assertEquals('Message 2 (en)', $this->translator->translate('Message 2', 'user'));
    }

    public function testTranslatorAddsPattern()
    {
        $this->translator->addTranslationPattern(
            'gettext',
            $this->testFilesDir . '/testmo',
            'translation-%s.mo'
        );

        $this->assertEquals('Message 1 (en)', $this->translator->translate('Message 1', 'default', 'en_US'));
        $this->assertEquals('Nachricht 1', $this->translator->translate('Message 1', 'default', 'de_DE'));
    }
}
