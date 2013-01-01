<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_File
 */

namespace Zend\File;

use SplFileInfo;

/**
 * Locate files containing PHP classes, interfaces, abstracts or traits
 *
 * @category   Zend
 * @package    Zend_File
 */
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
     * @param  string $class
     * @return PhpClassFile
     */
    public function addClass($class)
    {
        $this->classes[] = $class;
        return $this;
    }
}
