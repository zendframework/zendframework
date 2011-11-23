<?php

namespace Zend\Mail\Header;

class Bcc extends AbstractAddressList
{
    protected $fieldName = 'Bcc';
    protected static $type = 'bcc';
}
