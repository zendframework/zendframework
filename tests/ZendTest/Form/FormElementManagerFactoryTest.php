<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
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
use Zend\Session\Container as SessionContainer;
use Zend\Validator\Csrf;

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

    public function tearDown()
    {
        $ref = new \ReflectionClass('Zend\Validator\Csrf');
        $hashCache = $ref->getProperty('hashCache');
        $hashCache->setAccessible(true);
        $hashCache->setValue(new Csrf, array());
        SessionContainer::setDefaultManager(null);
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

    public function testCsrfCompatibility()
    {
        $_SESSION = array();
        $formClass = 'ZendTest\Form\TestAsset\CustomForm';
        $ref = new \ReflectionClass('Zend\Validator\Csrf');
        $hashPropRef = $ref->getProperty('hash');
        $hashPropRef->setAccessible(true);
        //check bare born
        $preForm = new $formClass;
        $csrf = $preForm->get('csrf')->getCsrfValidator();
        $this->assertNull($hashPropRef->getValue($csrf), 'Test "new Form" has no hash');
        //check FormElementManager
        $postForm = $this->manager->get($formClass);
        $postCsrf = $postForm->get('csrf')->getCsrfValidator();
        $this->assertNull($hashPropRef->getValue($postCsrf), 'Test "form from FormElementManager" has no hash');
    }

    public function testCsrfWorkFlow()
    {
        $_SESSION = array();
        $formClass = 'ZendTest\Form\TestAsset\CustomForm';
        $ref = new \ReflectionClass('Zend\Validator\Csrf');
        $hashPropRef = $ref->getProperty('hash');
        $hashPropRef->setAccessible(true);
        $hashCache = $ref->getProperty('hashCache');
        $hashCache->setAccessible(true);
        $hashCache->setValue(new Csrf, array());
        //check bare born
        $preForm = new $formClass;
        $preForm->prepare();
        $requestHash = $preForm->get('csrf')->getValue();
        SessionContainer::setDefaultManager(null);
        $hashCache->setValue(new Csrf, array());

        $postForm = $this->manager->get($formClass);
        $postCsrf = $postForm->get('csrf')->getCsrfValidator();
        $storedHash = $postCsrf->getHash();

        $this->assertEquals($requestHash, $storedHash, 'Test csrf validation');
    }
}
