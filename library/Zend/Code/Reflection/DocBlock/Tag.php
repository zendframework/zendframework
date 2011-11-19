<?php

namespace Zend\Code\Reflection\DocBlock;

interface Tag
{
    public function getName();
    public function initialize($content);
}