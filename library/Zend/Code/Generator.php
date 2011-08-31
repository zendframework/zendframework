<?php

namespace Zend\Code;

interface Generator
{
    public static function export();
    public function __toString();
}
