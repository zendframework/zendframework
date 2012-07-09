<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 */

namespace Zend\Mvc\Controller\Plugin;

use Zend\Stdlib\DispatchableInterface as Dispatchable;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Controller
 */
interface PluginInterface
{
    /**
     * Set the current controller instance
     *
     * @param  Dispatchable $controller
     * @return void
     */
    public function setController(Dispatchable $controller);

    /**
     * Get the current controller instance
     *
     * @return null|Dispatchable
     */
    public function getController();
}
