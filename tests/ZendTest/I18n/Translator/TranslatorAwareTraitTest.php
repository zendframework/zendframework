<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\I18n\Translator;

use \PHPUnit_Framework_TestCase as TestCase;
use \Zend\I18n\Translator\Translator;

class TranslatorAwareTraitTest extends TestCase
{
    /**
     * @requires PHP 5.4
     */
    public function testSetTranslator()
    {
        $object = new TestAsset\MockTranslatorAwareTrait;

        $this->assertAttributeEquals(null, 'translator', $object);

        $translator = new Translator;

        $object->setTranslator($translator);

        $this->assertAttributeEquals($translator, 'translator', $object);
    }

    /**
     * @requires PHP 5.4
     */
    public function testSetTranslatorAndTextDomain()
    {
        $object = new TestAsset\MockTranslatorAwareTrait;

        $this->assertAttributeEquals(null, 'translator', $object);
        $this->assertAttributeEquals('default', 'translatorTextDomain', $object);

        $translator = new Translator;
        $textDomain = 'domain';

        $object->setTranslator($translator, $textDomain);

        $this->assertAttributeEquals($translator, 'translator', $object);
        $this->assertAttributeEquals($textDomain, 'translatorTextDomain', $object);
    }

    /**
     * @requires PHP 5.4
     */
    public function testGetTranslator()
    {
        $object = new TestAsset\MockTranslatorAwareTrait;

        $this->assertNull($object->getTranslator());

        $translator = new Translator;

        $object->setTranslator($translator);

        $this->assertEquals($translator, $object->getTranslator());
    }

    /**
     * @requires PHP 5.4
     */
    public function testHasTranslator()
    {
        $object = new TestAsset\MockTranslatorAwareTrait;

        $this->assertFalse($object->hasTranslator());

        $translator = new Translator;

        $object->setTranslator($translator);

        $this->assertTrue($object->hasTranslator());
    }

    /**
     * @requires PHP 5.4
     */
    public function testSetTranslatorEnabled()
    {
        $object = new TestAsset\MockTranslatorAwareTrait;

        $this->assertAttributeEquals(true, 'translatorEnabled', $object);

        $enabled = false;

        $object->setTranslatorEnabled($enabled);

        $this->assertAttributeEquals($enabled, 'translatorEnabled', $object);

        $object->setTranslatorEnabled();

        $this->assertAttributeEquals(true, 'translatorEnabled', $object);
    }

    /**
     * @requires PHP 5.4
     */
    public function testIsTranslatorEnabled()
    {
        $object = new TestAsset\MockTranslatorAwareTrait;

        $this->assertTrue($object->isTranslatorEnabled());

        $object->setTranslatorEnabled(false);

        $this->assertFalse($object->isTranslatorEnabled());
    }

    /**
     * @requires PHP 5.4
     */
    public function testSetTranslatorTextDomain()
    {
        $object = new TestAsset\MockTranslatorAwareTrait;

        $this->assertAttributeEquals('default', 'translatorTextDomain', $object);

        $textDomain = 'domain';

        $object->setTranslatorTextDomain($textDomain);

        $this->assertAttributeEquals($textDomain, 'translatorTextDomain', $object);
    }

    /**
     * @requires PHP 5.4
     */
    public function testGetTranslatorTextDomain()
    {
        $object = new TestAsset\MockTranslatorAwareTrait;

        $this->assertEquals('default', $object->getTranslatorTextDomain());

        $textDomain = 'domain';

        $object->setTranslatorTextDomain($textDomain);

        $this->assertEquals($textDomain, $object->getTranslatorTextDomain());
    }
}
