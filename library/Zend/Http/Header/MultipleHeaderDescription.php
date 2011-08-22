<?php

namespace Zend\Http\Header;

interface MultipleHeaderDescription extends HeaderDescription
{
    /* public static function fromStringMultipleHeaders($headerLine); */
    public function toStringMultipleHeaders(array $headers);
}