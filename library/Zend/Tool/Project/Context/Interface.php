<?php

/**
 * Interface for contexts
 * 
 * setResource() is an optional method that if the context supports
 * will be set with the resource at construction time
 *
 */
interface Zend_Tool_Project_Context_Interface
{
    
    public function getName();
    
}
