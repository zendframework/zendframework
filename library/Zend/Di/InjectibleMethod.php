<?php
namespace Zend\Di;

interface InjectibleMethod
{
    public function __construct($name, array $params);
    public function getName();
    public function getParams();
}
