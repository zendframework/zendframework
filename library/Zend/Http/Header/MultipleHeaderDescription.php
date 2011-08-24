<?php

namespace Zend\Http\Header;

interface MultipleHeaderDescription extends HeaderDescription
{
    public function toStringMultipleHeaders(array $headers);
}