<?php

namespace Zend\Filter\Word\Service;

use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Filter\Word\SeparatorToSeparator;

class SeparatorToSeparatorFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
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
            isset($this->options['search_separator']) ? $this->options['search_separator'] : ' ',
            isset($this->options['replacement_separator']) ? $this->options['replacement_separator'] : '-'
        );
    }

}