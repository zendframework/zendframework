<?php

namespace Zend\Module\Consumer;

interface AutoUpgradable
{
    /**
     * Perform automatic upgrade tasks on the first run of the module
     * 
     * @param float $version
     * @return bool
     */
    public function autoUpgrade($version);
}
