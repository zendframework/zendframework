<?php
namespace Zend\Mail\Header;

interface StructuredHeader
{
    /**
     * Return the delimiter at which a header line should be wrapped
     * 
     * @return string
     */
    public function getDelimiter();
}
