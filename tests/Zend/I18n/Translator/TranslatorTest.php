<?php

namespace ZendTest\I18n\Translator;

use PHPUnit_Framework_TestCase as TestCase;
use Locale;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\TextDomain;
use Zend\I18n\Translator\Translator\Loader\LoaderInterface;
use ZendTest\I18n\Translator\TestAsset\Loader as TestLoader;

class TranslatorTest extends TestCase
{
    protected $originalLocale;
    protected $translator;

    public function setUp()
    {
        $this->originalLocale = Locale::getDefault();
        $this->translator     = new Translator();

        Locale::setDefault('en_EN');
    }

    public function tearDown()
    {
        Locale::setDefault($this->originalLocale);
    }

    public function testDefaultLocale()
    {
        $this->assertEquals('en_EN', $this->translator->getLocale());
    }

    public function testForcedLocale()
    {
        $this->translator->setLocale('de_DE');
        $this->assertEquals('de_DE', $this->translator->getLocale());
    }

    public function testTranslate()
    {
        $loader = new TestLoader();
        $loader->textDomain = new TextDomain(array('foo' => 'bar'));
        $this->translator->getPluginManager()->setService('test', $loader);
        $this->translator->addTranslationFile('test', null);
        
        $this->assertEquals('bar', $this->translator->translate('foo'));
    }
}
