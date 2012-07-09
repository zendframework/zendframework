<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_ModuleManager
 */

namespace Zend\ModuleManager\Listener;

use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleEvent;

/**
 * Init trigger
 * 
 * @category   Zend
 * @package    Zend_ModuleManager
 * @subpackage Listener
 */
class InitTrigger extends AbstractListener
{
    /**
     * @param ModuleEvent $e
     * @eturn void
     */
    public function __invoke(ModuleEvent $e)
    {
        $module = $e->getModule();
        if (!$module instanceof InitProviderInterface
            && !method_exists($module, 'init')
        ) {
            return;
        }

        $module->init($e->getTarget());
    }
}
