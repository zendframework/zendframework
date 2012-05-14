<?php

namespace Zend\Code\Reflection\DocBlock\Tag;

interface TagInterface
{
    public function getName();

    public function initialize($content);
}
