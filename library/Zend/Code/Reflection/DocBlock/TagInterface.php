<?php

namespace Zend\Code\Reflection\DocBlock;

interface TagInterface
{
    public function getName();
    public function initialize($content);
}
