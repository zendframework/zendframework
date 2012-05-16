<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Module
 */

namespace Zend\Module\Feature;

use Zend\EventManager\Event;

/**
 * Boostrap listener provider interface
 *
 * @category   Zend
 * @package    Zend_Module
 * @subpackage Feature
 */
interface BootstrapListenerInterface
{
    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function onBootstrap(Event $e);
}
