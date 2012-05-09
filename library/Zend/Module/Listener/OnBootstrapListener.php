<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Module
 */
namespace Zend\Module\Listener;

use Zend\Module\Consumer\BootstrapListenerInterface,
    Zend\Module\ModuleEvent;

/**
 * Autoloader listener
 * 
 * @category   Zend
 * @package    Zend_Module
 * @subpackage Listener
 */
class OnBootstrapListener extends AbstractListener
{

    /**
     * @param \Zend\Module\ModuleEvent $e
     * @return void
     */
    public function __invoke(ModuleEvent $e)
    {
        $module = $e->getModule();
        if (!$module instanceof BootstrapListenerInterface) {
            return;
        }

        $moduleManager = $e->getTarget();
        $events        = $moduleManager->events();
        $sharedEvents  = $events->getSharedManager();
        $sharedEvents->attach('bootstrap', 'bootstrap', array($module, 'onBootstrap'));
    }
}
