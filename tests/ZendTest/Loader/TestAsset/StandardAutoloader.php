<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Loader\TestAsset;

use Zend\Loader\StandardAutoloader as Psr0Autoloader;

/**
 * @group      Loader
 */
class StandardAutoloader extends Psr0Autoloader
{
    /**
     * Get registered namespaces
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * Get registered prefixes
     *
     * @return array
     */
    public function getPrefixes()
    {
        return $this->prefixes;
    }
}
