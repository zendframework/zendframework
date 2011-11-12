<?php

namespace Zend\Module\Consumer;

interface AutoInstallable
{
    /**
     * Perform automatic installation tasks on the first run of the module
     * 
     * @return bool
     */
    public function autoInstall();
}
