<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\I18n;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\I18n\Translator;

class TranslatorTest extends TestCase
{
    public function setUp()
    {
        $this->i18nTranslator = $this->getMock('Zend\I18n\Translator\Translator');
        $this->translator = new Translator($this->i18nTranslator);
    }

    public function testIsAnI18nTranslator()
    {
        $this->assertInstanceOf('Zend\I18n\Translator\TranslatorInterface', $this->translator);
    }

    public function testIsAValidatorTranslator()
    {
        $this->assertInstanceOf('Zend\Validator\Translator\TranslatorInterface', $this->translator);
    }

    public function testCanRetrieveComposedTranslator()
    {
        $this->assertSame($this->i18nTranslator, $this->translator->getTranslator());
    }

    public function testCanProxyToComposedTranslatorMethods()
    {
        $this->i18nTranslator->expects($this->once())
            ->method('setLocale')
            ->with($this->equalTo('en_US'));
        $this->translator->setLocale('en_US');
    }
}
