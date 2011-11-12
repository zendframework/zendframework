<?php

namespace Zend\Module\Listener;

class InitTrigger extends AbstractListener
{
    public function __invoke($e)
    {
        $module = $e->getParam('module');
        if (is_callable(array($module, 'init'))) {
            $module->init($e->getTarget());
        }
    }
}
