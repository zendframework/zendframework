<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Form;

use ArrayObject;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\Service\FormElementManagerFactory;
use Zend\Mvc\Service\DiFactory;
use Zend\Mvc\Service\DiAbstractServiceFactoryFactory;
use Zend\Mvc\Service\DiServiceInitializerFactory;

use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Exception;
use Zend\Form\FormElementManager;

class FormElementManagerFactoryTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    protected $services;

    /**
     * @var \Zend\Mvc\Controller\ControllerManager
     */
    protected $loader;

    public function setUp()
    {
        $formElementManagerFactory = new FormElementManagerFactory();
        $config = new ArrayObject(array('di' => array()));
        $services = $this->services = new ServiceManager();
        $services->setService('Zend\ServiceManager\ServiceLocatorInterface', $services);
        $services->setFactory('FormElementManager', $formElementManagerFactory);
        $services->setService('Config', $config);
        $services->setFactory('Di', new DiFactory());
        $services->setFactory('DiAbstractServiceFactory', new DiAbstractServiceFactoryFactory());
        $services->setFactory('DiServiceInitializer', new DiServiceInitializerFactory());

        $this->manager = $services->get('FormElementManager');

        $this->standaloneManager = new FormElementManager();
    }

    public function testWillInstantiateFormFromInvokable()
    {
        $form = $this->manager->get('form');
        $this->assertInstanceof('Zend\Form\Form', $form);
    }

    public function testWillInstantiateFormFromDiAbstractFactory()
    {
        //without DiAbstractFactory
        $this->assertFalse($this->standaloneManager->has('ZendTest\Form\TestAsset\CustomForm'));
        //with DiAbstractFactory
        $this->assertTrue($this->manager->has('ZendTest\Form\TestAsset\CustomForm'));
        $form = $this->manager->get('ZendTest\Form\TestAsset\CustomForm');
        $this->assertInstanceof('ZendTest\Form\TestAsset\CustomForm', $form);
    }

    public function testNoWrapFieldName()
    {
        $form = $this->manager->get('ZendTest\Form\TestAsset\CustomForm');
        $this->assertFalse($form->wrapElements(), 'ensure wrapElements option');
        $this->assertTrue($form->has('email'), 'ensure the form has email element');
        $emailElement = $form->get('email');
        $this->assertEquals('email', $emailElement->getName());
    }
}
