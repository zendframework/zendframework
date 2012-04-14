<?php

namespace ZendTest\Config\Writer\files;

class PhpReader {
    public function fromFile($filename) {
        return include $filename;
    }
}