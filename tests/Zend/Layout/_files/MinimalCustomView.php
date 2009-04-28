<?php

class Zend_Layout_Test_MinimalCustomView implements Zend_View_Interface
{
    
    public function getEngine() {}

    public function setScriptPath($path) {}

    public function getScriptPaths() {}

    public function setBasePath($path, $classPrefix = 'Zend_View') {}

    public function addBasePath($path, $classPrefix = 'Zend_View') {}

    public function __set($key, $val) {}

    public function __isset($key) {}

    public function __unset($key) {}

    public function assign($spec, $value = null) {}

    public function clearVars() {}

    public function render($name) {}
    
}