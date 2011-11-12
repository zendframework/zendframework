<?php

namespace Zend\Module\Listener;

interface ConfigMerger
{
    public function getMergedConfig($returnConfigAsObject = true);
}
