<?php

namespace Zend\Module\Listener;

class AutoInstallUpgradeTrigger extends AbstractListener
{
    public function __invoke($e)
    {
        $module = $e->getParam('module');
        
        if ((!$module instanceof AutoInstallable)
            && (!$module instanceof AutoUpgradable)
        ) {
            return;
        }

        if ($module instanceof AutoInstallable) {
            //..
        }
        $autoloaderConfig = $module->getAutoloaderConfig();
        AutoloaderFactory::factory($autoloaderConfig);
    }

    
    /**
     * get manifest of currently installed modules
     * 
     * @return Config
     */
    public function loadInstallationManifest()
    {
        $path = $this->getOptions()->getManifestDir() . '/manifest.php';
        if (file_exists($path)) {
            $this->manifest = new Config(include $path, true);
        } else {
            $this->manifest = new Config(array(), true);
        }
        return $this;
    }
    
    public function saveInstallationManifest()
    {
        if ($this->manifest->get('_dirty', false)) {
            unset($this->manifest->{'_dirty'});
            $path = $this->getOptions()->getManifestDir() . '/manifest.php';
            $writer = new ArrayWriter();
            $writer->write($path, $this->manifest);
        }
        return $this;
    }
}
