<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\I18n\Translator;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\I18n\Translator\TranslatorServiceFactory;

class TranslatorServiceFactoryTest extends TestCase
{
    public function testCreateServiceWithNoTranslatorKeyDefined()
    {
        $slContents = array(array('Configuration', array()));
        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');
        $serviceLocator->expects($this->once())
                       ->method('get')
                       ->will($this->returnValueMap($slContents));

        $factory = new TranslatorServiceFactory();
        $translator = $factory->createService($serviceLocator);
        $this->assertInstanceOf('Zend\I18n\Translator\Translator', $translator);
    }
}
