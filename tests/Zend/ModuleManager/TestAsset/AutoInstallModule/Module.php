<?php

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
