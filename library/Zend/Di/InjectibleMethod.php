<?php
namespace Zend\Di;

interface InjectibleMethod
{
    public function __construct($name, array $params = null, array $paramMap = null);
    public function getName();
    public function setParams(array $params);
    public function getParams();
    public function setClass($class);
    public function getClass();
    public function setParamMap(array $map);
    public function getParamMap();
}
