<?php

namespace Zend\Http\Header;

interface MultipleHeaderInterface extends HeaderInterface
{
    public function toStringMultipleHeaders(array $headers);
}
