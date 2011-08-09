<?php

namespace Zend\Http\Header;

interface MultipleHeaderDescription
{
    public static function fromStringMultipleHeaders($headerLine);
    public function toStringMultipleHeaders(array $headers);
}