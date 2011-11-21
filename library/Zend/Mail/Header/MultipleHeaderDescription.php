<?php

namespace Zend\Mail\Header;

interface MultipleHeaderDescription extends HeaderDescription
{
    public function toStringMultipleHeaders(array $headers);
}
