<?php

namespace Zend\Filter\Word\Service;

use Zend\Filter\Word\SeparatorToSeparator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\MutableCreationOptionsTrait;
use Zend\ServiceManager\ServiceLocatorInterface;

class SeparatorToSeparatorFactory implements
    FactoryInterface,
    MutableCreationOptionsInterface
{
    use MutableCreationOptionsTrait;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->setCreationOptions($options);
    }

    /**
     * {@inheritDoc}
     *
     * @return SeparatorToSeparator
     * @throws ServiceNotCreatedException if Controllermanager service is not found in application service locator
     */
    public function createService(ServiceLocatorInterface $plugins)
    {
        return new SeparatorToSeparator(
            isset($this->creationOptions['search_separator']) ? $this->creationOptions['search_separator'] : ' ',
            isset($this->creationOptions['replacement_separator']) ? $this->creationOptions['replacement_separator'] : '-'
        );
    }
}
