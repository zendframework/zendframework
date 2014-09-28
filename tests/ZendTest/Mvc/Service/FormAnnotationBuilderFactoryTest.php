<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Service;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\Service\FormAnnotationBuilderFactory;
use Zend\ServiceManager\ServiceManager;

class FormAnnotationBuilderFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $mockElementManager = $this->getMock('Zend\Form\FormElementManager');
        $mockValidatorManager = $this->getMock('Zend\Validator\ValidatorPluginManager');
        $mockFilterManager = $this->getMock('Zend\Filter\FilterPluginManager');

        $serviceLocator = new ServiceManager();
        $serviceLocator->setService('FormElementManager', $mockElementManager);
        $serviceLocator->setService('ValidatorManager', $mockValidatorManager);
        $serviceLocator->setService('FilterManager', $mockFilterManager);

        $sut = new FormAnnotationBuilderFactory();

        $this->assertInstanceOf('\Zend\Form\Annotation\AnnotationBuilder', $sut->createService($serviceLocator));
    }
}