<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form;

use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceManagerInterface;

class FormAbstractFactory implements AbstractFactoryInterface
{
    protected $configKey     = 'form_manager';
    protected $servicePrefix = 'Form\\';

    /**
     * Can we create the requested service?
     * 
     * @param  ServiceManagerInterface $services 
     * @param  string $name Service name (as resolved by ServiceManager)
     * @param  string $rName Name by which service was requested
     * @return bool
     */
    public function canCreateServiceWithName(ServiceManagerInterface $services, $name, $rName)
    {
        if (!$services->has('Config')) {
            return false;
        }

        $prefixLength = strlen($this->servicePrefix);
        if (strlen($rName) < $prefixLength
            || substr($rName, 0, $prefixLength) !== $this->servicePrefix
        ) {
            return false;
        }

        $config = $services->get('Config');
        if (!isset($config[$this->configKey]) 
            || !is_array($config[$this->configKey]) 
            || empty($config[$this->configKey])
        ) {
            return false;
        }

        $config = $config[$this->configKey];

        $serviceName = substr($rName, $prefixLength);
        if (!isset($config[$serviceName])
            || !is_array($config[$serviceName])
            || empty($config[$serviceName])
        ) {
            return false;
        }

        return true;
    }

    /**
     * Create a form
     * 
     * @param  ServiceManagerInterface $services 
     * @param  string $name Service name (as resolved by ServiceManager)
     * @param  string $rName Name by which service was requested
     * @return Form
     */
    public function createServiceWithName(ServiceManagerInterface $services, $name, $rName)
    {
        $serviceName = substr($rName, strlen($this->servicePrefix));
        $config = $services->get('Config');

        $config = $config[$this->configKey][$serviceName];

        $factory = $this->getFormFactory();
        $this->marshalInputFilter($config, $services, $factory);

        return $factory->createForm($config);
    }

    protected function getFormFactory(ServiceManagerInterface $services)
    {
        if ($this->factory instanceof Factory) {
            return $this->factory;
        }

        $elements = null;
        if ($services->has('FormElementManager')) {
            $elements = $services->get('FormElementManager');
        }

        $this->factory = new Factory($elements);
        return $this->factory;
    }

    protected function marshalInputFilter(array &$config, ServiceManagerInterface $services, Factory $formFactory)
    {
        if (!isset($config['input_filter'])) {
            return;
        }

        if ($config['input_filter'] instanceof InputFilterInterface) {
            return;
        }

        if (is_string($config['input_filter'])
            && $services->has('InputFilterManager')
        ) {
            $inputFilters = $services->get('InputFilterManager');
            if ($inputFilters->has($config['input_filter'])) {
                $config['input_filter'] = $inputFilters->get($config['input_filter']);
                return;
            }
        }

        $inputFilterFactory = $formFactory->getInputFilterFactory();
        $inputFilterFactory->getDefaultFilterChain()->setPluginManager($services->get('FilterManager'));
        $inputValidatorFactory->getDefaultValidatorChain()->setPluginManager($services->get('ValidatorManager'));
    }
}
