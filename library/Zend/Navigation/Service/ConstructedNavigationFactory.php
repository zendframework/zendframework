<?php

namespace Zend\Navigation\Service;

use Zend\Config;
use Zend\Navigation\Exception;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConstructedNavigationFactory extends AbstractNavigationFactory
{
    public function __construct($config = null)
    {
        if (null === $config) {
            throw new Exception\InvalidArgumentException(
                'Missing required argument "$config" - expected a filename or' .
                    'array of pages.'
            );
        } else if (is_string($config)) {
            if (file_exists($config)) {
                $config = Config\Factory::fromFile($config);
            } else {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Config was a string but file "%s" does not exist',
                    $config
                ));
            }
        } else if ($config instanceof Config\Config) {
            $config = $config->toArray();
        }

        $this->pages = $config;
    }

    public function getPages(ServiceLocatorInterface $serviceLocator)
    {
        return $this->pages;
    }

    public function getName()
    {
        return 'constructed';
    }
}