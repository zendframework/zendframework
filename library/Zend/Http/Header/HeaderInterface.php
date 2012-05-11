<?php

namespace Zend\Http\Header;

interface HeaderInterface
{
    public static function fromString($headerLine);
    public function getFieldName();
    public function getFieldValue();
    public function toString();
}
