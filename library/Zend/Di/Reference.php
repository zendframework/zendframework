<?php
namespace Zend\Di;

/**
 * A reference to a service managed by a DependencyInjection manager.
 *
 * Used when traversing dependencies to indicate an object managed by the DI 
 * manager.
 * 
 * @copyright Copyright (C) 2006-Present, Zend Technologies, Inc.
 * @license   New BSD {@link http://framework.zend.com/license/new-bsd}
 */
class Reference implements DependencyReference
{
    /**
     * Service this object serves as a reference for
     * @var string
     */
    protected $name;

    /**
     * Construct reference object 
     *
     * @param  string $serviceName 
     * @return void
     */
    public function __construct($serviceName)
    {
        $this->name = $serviceName;
    }

    /**
     * Retrieve service name
     * 
     * @return string
     */
    public function getServiceName()
    {
        return $this->name;
    }
}
