<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Navigation
 */

namespace Zend\Navigation\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Constructed factory to set pages during construction.
 *
 * @category  Zend
 * @package   Zend_Navigation
 */
class ConstructedNavigationFactory extends AbstractNavigationFactory
{
    /**
     * @param string|\Zend\Config\Config|array $config
     */
    public function __construct($config)
    {
        $this->pages = $this->getPagesFromConfig($config);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return array|null|\Zend\Config\Config
     */
    public function getPages(ServiceLocatorInterface $serviceLocator)
    {
        return $this->pages;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'constructed';
    }
}
