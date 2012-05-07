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

use Zend\Module\ModuleEvent;

/**
 * Init trigger
 * 
 * @category   Zend
 * @package    Zend_Module
 * @subpackage Listener
 */
class InitTrigger extends AbstractListener
{
    /**
     * @param \Zend\Module\ModuleEvent $e
     * @eturn void
     */
    public function __invoke(ModuleEvent $e)
    {
        $module = $e->getModule();
        if (is_callable(array($module, 'init'))) {
            $module->init($e->getTarget());
        }
    }
}
