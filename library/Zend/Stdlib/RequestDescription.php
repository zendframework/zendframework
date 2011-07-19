<?php

namespace Zend\Stdlib;

interface RequestDescription extends MessageDescription
{
    public function __toString();
    public function fromString($string);
}
