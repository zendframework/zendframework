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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;
use Zend\View\Helper;
use Zend\Translator;
use Zend\View;

/**
 * Test class for Zend_View_Helper_Translator.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class TranslateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_Translator
     */
    public $helper;

    /**
     * @var string
     */
    public $basePath;


    public function clearRegistry()
    {
        $regKey = 'Zend_Translator';
        if (\Zend\Registry::isRegistered($regKey)) {
            $registry = \Zend\Registry::getInstance();
            unset($registry[$regKey]);
        }
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->clearRegistry();
        $this->helper = new Helper\Translator();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
        $this->clearRegistry();
    }

    public function testTranslationObjectPassedToConstructorUsedForTranslation()
    {
        $trans = new Translator\Translator('arrayAdapter', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');

        $helper = new Helper\Translator($trans);
        $this->assertEquals('eins', $helper->__invoke('one'));
        $this->assertEquals('three', $helper->__invoke('three'));
    }

    public function testLocalTranslationObjectUsedForTranslationsWhenPresent()
    {
        $trans = new Translator\Translator('arrayAdapter', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');

        $this->helper->setTranslator($trans);
        $this->assertEquals('eins', $this->helper->__invoke('one'));
        $this->assertEquals('three', $this->helper->__invoke('three'));
    }

    public function testTranslationObjectInRegistryUsedForTranslationsInAbsenceOfLocalTranslationObject()
    {
        $trans = new Translator\Translator('arrayAdapter', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');
        \Zend\Registry::set('Zend_Translator', $trans);
        $this->assertEquals('eins', $this->helper->__invoke('one'));
    }

    public function testOriginalMessagesAreReturnedWhenNoTranslationObjectPresent()
    {
        $this->assertEquals('one', $this->helper->__invoke('one'));
        $this->assertEquals('three', $this->helper->__invoke('three'));
    }

    public function testPassingNonNullNonTranslationObjectToConstructorThrowsException()
    {
        try {
            $helper = new Helper\Translator('something');
        } catch (View\Exception $e) {
            $this->assertContains('must set an instance of Zend\Translator', $e->getMessage());
        }
    }

    public function testPassingNonTranslationObjectToSetTranslatorThrowsException()
    {
        try {
            $this->helper->setTranslator('something');
        } catch (View\Exception $e) {
            $this->assertContains('must set an instance of Zend\Translator', $e->getMessage());
        }
    }

    public function testRetrievingLocaleWhenNoTranslationObjectSetThrowsException()
    {
        try {
            $this->helper->getLocale();
        } catch (View\Exception $e) {
            $this->assertContains('must set an instance of Zend\Translator', $e->getMessage());
        }
    }

    public function testSettingLocaleWhenNoTranslationObjectSetThrowsException()
    {
        try {
            $this->helper->setLocale('de');
        } catch (View\Exception $e) {
            $this->assertContains('must set an instance of Zend\Translator', $e->getMessage());
        }
    }

    public function testCanSetLocale()
    {
        $trans = new Translator\Translator('arrayAdapter', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');
        $trans->addTranslation(array('one' => 'uno', 'two %1\$s' => 'duo %2\$s'), 'it');
        $trans->setLocale('de');

        $this->helper->setTranslator($trans);
        $this->assertEquals('eins', $this->helper->__invoke('one'));
        $new = $this->helper->setLocale('it');
        $this->assertTrue($new instanceof Helper\Translator);
        $this->assertEquals('it', $new->getLocale());
        $this->assertEquals('uno', $this->helper->__invoke('one'));
    }

    public function testHelperImplementsFluentInterface()
    {
        $trans = new Translator\Translator('arrayAdapter', array('one' => 'eins', 'two %1\$s' => 'zwei %1\$s'), 'de');
        $trans->addTranslation(array('one' => 'uno', 'two %1\$s' => 'duo %2\$s'), 'it');
        $trans->setLocale('de');

        $locale = $this->helper->__invoke()->setTranslator($trans)->getLocale();

        $this->assertEquals('de', $locale);
    }

    public function testCanTranslateWithOptions()
    {
        $trans = new Translator\Translator('arrayAdapter', array('one' => 'eins', "two %1\$s" => "zwei %1\$s",
            "three %1\$s %2\$s" => "drei %1\$s %2\$s"), 'de');
        $trans->addTranslation(array('one' => 'uno', "two %1\$s" => "duo %2\$s",
            "three %1\$s %2\$s" => "tre %1\$s %2\$s"), 'it');
        $trans->setLocale('de');

        $this->helper->setTranslator($trans);
        $this->assertEquals("drei 100 200", $this->helper->__invoke("three %1\$s %2\$s", "100", "200"));
        $this->assertEquals("tre 100 200", $this->helper->__invoke("three %1\$s %2\$s", "100", "200", 'it'));
        $this->assertEquals("drei 100 it", $this->helper->translate("three %1\$s %2\$s", "100", "it"));
        $this->assertEquals("drei 100 200", $this->helper->__invoke("three %1\$s %2\$s", array("100", "200")));
        $this->assertEquals("tre 100 200", $this->helper->__invoke("three %1\$s %2\$s", array("100", "200"), 'it'));
    }

    public function testTranslationObjectNullByDefault()
    {
        $this->assertNull($this->helper->getTranslator());
    }

    public function testLocalTranslationObjectIsPreferredOverRegistry()
    {
        $transReg = new Translator\Translator('arrayAdapter', array('one' => 'eins'));
        \Zend\Registry::set('Zend_Translator', $transReg);

        $this->assertSame($transReg->getAdapter(), $this->helper->getTranslator());

        $transLoc = new Translator\Translator('arrayAdapter', array('one' => 'uno'));
        $this->helper->setTranslator($transLoc);
        $this->assertSame($transLoc->getAdapter(), $this->helper->getTranslator());
        $this->assertNotSame($transLoc->getAdapter(), $transReg->getAdapter());
    }

    public function testHelperObjectReturnedWhenNoArgumentsPassed()
    {
        $helper = $this->helper->__invoke();
        $this->assertSame($this->helper, $helper);

        $transLoc = new Translator\Translator('arrayAdapter', array('one' => 'eins'));
        $this->helper->setTranslator($transLoc);
        $helper = $this->helper->__invoke();
        $this->assertSame($this->helper, $helper);
    }

    /**
     * ZF-6724
     */
    public function testTranslationWithPercent()
    {
        $trans = new Translator\Translator('arrayAdapter', array('one' => 'eins', "two %1\$s" => "zwei %1\$s",
            "three %1\$s %2\$s" => "drei %1\$s %2\$s", 'vier%ig' => 'four%'), 'de');
        $trans->setLocale('de');

        $this->helper->setTranslator($trans);
        $this->assertEquals("four%", $this->helper->__invoke("vier%ig"));
        $this->assertEquals("zwei 100", $this->helper->__invoke("two %1\$s", "100"));
    }

    /**
     * ZF-7937
     */
    public function testTranslationWithoutTranslator()
    {
        $result = $this->helper->__invoke("test %1\$s", "100");
        $this->assertEquals('test 100', $result);
    }

    /**
     * @group ZF2-140
     */
    public function testSetTranslatorWithTranslationAdapter()
    {
        $trans = new Translator\Adapter\ArrayAdapter(array('one' => 'eins', "two %1\$s" => "zwei %1\$s",
            "three %1\$s %2\$s" => "drei %1\$s %2\$s", 'vier%ig' => 'four%'), 'de');
        $this->helper->setTranslator($trans);
    }

    /**
     * @group ZF2-140
     */
    public function testSetTranslatorWithTranslation()
    {
        $trans = new Translator\Translator('arrayAdapter', array('one' => 'eins', "two %1\$s" => "zwei %1\$s",
            "three %1\$s %2\$s" => "drei %1\$s %2\$s", 'vier%ig' => 'four%'), 'de');
        $this->helper->setTranslator($trans);
    }
}
