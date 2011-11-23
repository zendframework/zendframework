<?php

namespace Zend\Mail\Header;

class To extends AbstractAddressList
{
    protected $fieldName = 'To';
    protected static $type = 'to';
}
