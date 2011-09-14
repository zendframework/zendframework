<?php
namespace Zend\Di;

interface Locator
{
    /**
     * Retrieve a class instance
     * 
     * @param  string $name Class name or service name
     * @param  null|array $params Parameters to be used when instantiating a new instance of $name
     * @return object|null
     */
    public function get($name, array $params = null);
}
