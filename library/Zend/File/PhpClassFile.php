<?php

namespace Zend\File;

use SplFileInfo;

class PhpClassFile extends SplFileInfo
{

    /**
     * @var array
     */
    protected $classes;

    /**
     * Get classes
     *
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Add class
     *
     * @param string $class
     * @return PhpClassFile
     */
    public function addClass($class)
    {
        $this->classes[] = $class;
        return $this;
    }
}