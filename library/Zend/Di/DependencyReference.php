<?php
namespace Zend\Di;

interface DependencyReference
{
    public function __construct($serviceName);
    public function getServiceName();
}
