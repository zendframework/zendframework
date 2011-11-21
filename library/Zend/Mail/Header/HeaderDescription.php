<?php

namespace Zend\Mail\Header;

interface HeaderDescription
{
    public static function fromString($headerLine);
    public function getFieldName();
    public function getFieldValue();
    public function toString();
}
