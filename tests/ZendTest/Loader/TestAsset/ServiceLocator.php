<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Loader
 */

namespace ZendTest\Loader\TestAsset;

use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceLocator implements ServiceLocatorInterface
{
    protected $services = array();

    public function get($name, array $params = array())
    {
        if (!isset($this->services[$name])) {
            return null;
        }

        return $this->services[$name];
    }

    public function has($name)
    {
        return (isset($this->services[$name]));
    }

    public function set($name, $object)
    {
        $this->services[$name] = $object;
    }
}
