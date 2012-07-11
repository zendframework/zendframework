<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace AutoInstallModule;

class Module
{
    public static $VERSION = '1.0.0';
    public static $RESPONSE = true;

    public function autoInstall()
    {
        return $this->install();
    }

    public function autoUpgrade($version = null)
    {
        return $this->upgrade($version);
    }

    public function install()
    {
        return static::$RESPONSE;
    }

    public function upgrade($version = null)
    {
        return static::$RESPONSE;
    }

    public function getProvides()
    {
        return array(
            __NAMESPACE__ => array(
                'version' => static::$VERSION,
            ),
        );
    }
}
