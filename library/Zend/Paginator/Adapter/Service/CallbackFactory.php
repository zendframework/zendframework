<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Paginator\Adapter\Service;

use Zend\Paginator\Adapter\Callback;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CallbackFactory implements
    FactoryInterface,
    MutableCreationOptionsInterface
{
    /**
     * Adapter options
     * @var array
     */
    protected $creationOptions;

    /**
     * {@inheritDoc}
     */
    public function setCreationOptions(array $creationOptions)
    {
        $this->creationOptions = $creationOptions;
    }

    /**
     * {@inheritDoc}
     *
     * @return Callback
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Callback($this->creationOptions[0], $this->creationOptions[1]);
    }
}
