<?php
namespace Zend\Di;

interface InjectibleMethod
{
    public function __construct($name, array $args);
    public function getName();
    public function getArgs();
}
