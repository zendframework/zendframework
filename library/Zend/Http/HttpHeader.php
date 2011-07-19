<?php

namespace Zend\Http;

interface HttpHeader
{
    public function __construct($header, $value = null, $replace = false);

    /* mutators */
    public function setType($type);
    public function setValue($value);
    public function replace($flag = null); // also acts as mutator

    /* accessors */
    public function getType();
    public function getValue();

    /* behaviors */
    public function send();
    public function __toString();
}
