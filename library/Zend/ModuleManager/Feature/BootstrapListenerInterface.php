<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_ModuleManager
 */

namespace Zend\ModuleManager\Feature;

use Zend\EventManager\Event;

/**
 * Boostrap listener provider interface
 *
 * @category   Zend
 * @package    Zend_ModuleManager
 * @subpackage Feature
 */
interface BootstrapListenerInterface
{
    /**
     * Listen to the bootstrap event
     *
     * @return array
     */
    public function onBootstrap(Event $e);
}
