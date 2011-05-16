<?php
namespace Zend\Di;

use Iterator;

interface InjectibleMethods extends Iterator
{
    public function insert(InjectibleMethod $method);

    /**
     * @return MethodDefinition
     */
    // public function current();
    
    /**
     * @return string Method name
     */
    // public function key();
}
