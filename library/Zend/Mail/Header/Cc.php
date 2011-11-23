<?php

namespace Zend\Mail\Header;

class Cc extends AbstractAddressList
{
    protected $fieldName = 'Cc';
    protected static $type = 'cc';
}
