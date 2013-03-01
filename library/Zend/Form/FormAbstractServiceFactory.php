<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Config\Factory;

/**
 * Abstract form factory.
 *
 * Allow create forms via specification defined in config file.
 * Reserved <b>form</b> section.
 */
class FormAbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @var \Zend\Form\Factory
     */
    private $formFactory;

    /**
     * @see \Zend\ServiceManager\AbstractFactoryInterface::canCreateServiceWithName()
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('Config');

        if (isset($config['form'][$name])) {
            return true;

        } else if (isset($config['form'][$requestedName])) {
            return true;

        } else {
            return false;
        }
    }

    /**
     * @see \Zend\ServiceManager\AbstractFactoryInterface::createServiceWithName()
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('Config');

        if (isset($config['form'][$name])) {
            return $this->createForm($config['form'][$name]);

        } else if (isset($config['form'][$requestedName])) {
            return $this->createForm($config['form'][$requestedName]);

        } else {
            return $this->createForm();
        }
    }

    /**
     * @param array $spec
     * @return \Zend\Form\FormInterface
     */
    public function createForm($spec = array())
    {
        $factory = $this->getFormFactory();
        $form = $factory->create($spec);
        $form->setFormFactory($factory);

        return $form;
    }

    /**
     * @param Factory $formFactory
     */
    public function setFormFactory(Factory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @return \Zend\Form\Factory
     */
    public function getFormFactory()
    {
        if (null === $this->formFactory) {
            $this->setFormFactory(new Factory());
        }
        return $this->formFactory;
    }
}
