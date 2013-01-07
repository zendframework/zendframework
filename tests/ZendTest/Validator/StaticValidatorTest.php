<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator;

use Zend\I18n\Translator;
use Zend\Validator\AbstractValidator;
use Zend\I18n\Validator\Alpha;
use Zend\Validator\Between;
use Zend\Validator\StaticValidator;
use Zend\Validator\ValidatorPluginManager;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class StaticValidatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var Alpha */
    public $validator;

    /**
     * Whether an error occurred
     *
     * @var bool
     */
    protected $errorOccurred = false;

    /**
     * Creates a new validation object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        AbstractValidator::setDefaultTranslator(null);
        StaticValidator::setPluginManager(null);
        $this->validator = new Alpha();
    }

    public function tearDown()
    {
        AbstractValidator::setDefaultTranslator(null);
        AbstractValidator::setMessageLength(-1);
    }

    /**
     * Ignores a raised PHP error when in effect, but throws a flag to indicate an error occurred
     *
     * @param  integer $errno
     * @param  string  $errstr
     * @param  string  $errfile
     * @param  integer $errline
     * @param  array   $errcontext
     * @return void
     */
    public function errorHandlerIgnore($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $this->errorOccurred = true;
    }

    public function testCanSetGlobalDefaultTranslator()
    {
        $translator = new Translator\Translator();
        AbstractValidator::setDefaultTranslator($translator);
        $this->assertSame($translator, AbstractValidator::getDefaultTranslator());
    }

    public function testGlobalDefaultTranslatorUsedWhenNoLocalTranslatorSet()
    {
        $this->testCanSetGlobalDefaultTranslator();
        $this->assertSame(AbstractValidator::getDefaultTranslator(), $this->validator->getTranslator());
    }

    public function testLocalTranslatorPreferredOverGlobalTranslator()
    {
        $this->testCanSetGlobalDefaultTranslator();
        $translator = new Translator\Translator();
        $this->validator->setTranslator($translator);
        $this->assertNotSame(AbstractValidator::getDefaultTranslator(), $this->validator->getTranslator());
    }

    public function testMaximumErrorMessageLength()
    {
        $this->assertEquals(-1, AbstractValidator::getMessageLength());
        AbstractValidator::setMessageLength(10);
        $this->assertEquals(10, AbstractValidator::getMessageLength());

        $loader = new TestAsset\ArrayTranslator();
        $loader->translations = array(
            Alpha::INVALID => 'This is the translated message for %value%',
        );
        $translator = new Translator\Translator();
        $translator->getPluginManager()->setService('default', $loader);
        $translator->addTranslationFile('default', null);

        $this->validator->setTranslator($translator);
        $this->assertFalse($this->validator->isValid(123));
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists(Alpha::INVALID, $messages));
        $this->assertEquals('This is...', $messages[Alpha::INVALID]);
    }

    public function testSetGetMessageLengthLimitation()
    {
        AbstractValidator::setMessageLength(5);
        $this->assertEquals(5, AbstractValidator::getMessageLength());

        $valid = new Between(1, 10);
        $this->assertFalse($valid->isValid(24));
        $message = current($valid->getMessages());
        $this->assertTrue(strlen($message) <= 5);
    }

    public function testSetGetDefaultTranslator()
    {
        $translator = new Translator\Translator();
        AbstractValidator::setDefaultTranslator($translator);
        $this->assertSame($translator, AbstractValidator::getDefaultTranslator());
    }

    /* plugin loading */

    public function testLazyLoadsValidatorPluginManagerByDefault()
    {
        $plugins = StaticValidator::getPluginManager();
        $this->assertInstanceOf('Zend\Validator\ValidatorPluginManager', $plugins);
    }

    public function testCanSetCustomPluginManager()
    {
        $plugins = new ValidatorPluginManager();
        StaticValidator::setPluginManager($plugins);
        $this->assertSame($plugins, StaticValidator::getPluginManager());
    }

    public function testPassingNullWhenSettingPluginManagerResetsPluginManager()
    {
        $plugins = new ValidatorPluginManager();
        StaticValidator::setPluginManager($plugins);
        $this->assertSame($plugins, StaticValidator::getPluginManager());
        StaticValidator::setPluginManager(null);
        $this->assertNotSame($plugins, StaticValidator::getPluginManager());
    }

    public function testExecuteValidWithParameters()
    {
        $this->assertTrue(StaticValidator::execute(5, 'Between', array(1, 10)));
        $this->assertTrue(StaticValidator::execute(5, 'Between', array('min' => 1, 'max' => 10)));
    }
}
