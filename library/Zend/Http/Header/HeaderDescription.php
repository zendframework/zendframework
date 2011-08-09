<?php

namespace Zend\Http\Header;

interface HeaderDescription
{
    public static function fromString($headerLine);
    public function getName();
    public function getValue();
    public function toString();
}
