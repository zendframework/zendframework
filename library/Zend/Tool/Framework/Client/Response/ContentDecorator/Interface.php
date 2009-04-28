<?php

interface Zend_Tool_Framework_Client_Response_ContentDecorator_Interface
{
    
    public function getName();

    public function decorate($content, $decoratorValue);
    
}
