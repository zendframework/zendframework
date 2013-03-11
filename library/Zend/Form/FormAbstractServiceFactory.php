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
use Zend\Form\Factory;

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
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('Config');

        return isset($config['form'][$requestedName]);
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('Config');

        return $this->createForm($serviceLocator, $config['form'][$requestedName]);
    }

    /**
     * @param array $spec
     * @return \Zend\Form\FormInterface
     */
    public function createForm(ServiceLocatorInterface $serviceLocator, $spec = array())
    {
        $factory = $this->getFormFactory($serviceLocator);
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
    public function getFormFactory(ServiceLocatorInterface $serviceLocator)
    {
        if (null === $this->formFactory) {
            $formElementManager = $serviceLocator->has('Zend\Form\FormElementManager')
                ? $serviceLocator->get('Zend\Form\FormElementManager') : null;

            $this->setFormFactory(new Factory($formElementManager));
        }
        return $this->formFactory;
    }
}
