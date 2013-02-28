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

use Zend\Validator\ValidatorPluginManager;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class ValidatorPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->validators = new ValidatorPluginManager();
    }

    public function testAllowsInjectingTranslator()
    {
        $translator = $this->getMock("Zend\I18n\Translator\Translator");

        $slContents = array(array('translator', $translator));
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->once())
            ->method('get')
            ->will($this->returnValueMap($slContents));
        $serviceLocator->expects($this->once())
            ->method('has')
            ->with($this->equalTo('translator'))
            ->will($this->returnValue(true));

        $this->validators->setServiceLocator($serviceLocator);
        $this->assertSame($serviceLocator, $this->validators->getServiceLocator());

        $validator = $this->validators->get('notempty');
        $this->assertSame($translator, $validator->getTranslator());
    }

    public function testNoTranslatorInjectedWhenTranslatorIsNotPresent()
    {
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->once())
            ->method('has')
            ->with($this->equalTo('translator'))
            ->will($this->returnValue(false));

        $this->validators->setServiceLocator($serviceLocator);
        $this->assertSame($serviceLocator, $this->validators->getServiceLocator());

        $validator = $this->validators->get('notempty');
        $this->assertNull($validator->getTranslator());
    }

    public function testRegisteringInvalidValidatorRaisesException()
    {
        $this->setExpectedException('Zend\Validator\Exception\RuntimeException');
        $this->validators->setService('test', $this);
    }

    public function testLoadingInvalidValidatorRaisesException()
    {
        $this->validators->setInvokableClass('test', get_class($this));
        $this->setExpectedException('Zend\Validator\Exception\RuntimeException');
        $this->validators->get('test');
    }
}
