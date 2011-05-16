<?php
namespace Zend\Di;

interface DependencyEnabled
{
    public function setInjector(DependencyInjection $di);
    public function getInjector();
}
