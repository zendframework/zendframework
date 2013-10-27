<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator;

use Zend\Stdlib\Hydrator\Naming\NamingInterface;

interface NamingEnabledInterface
{
    /**
     * Adds the given naming under the given name.
     *
     * @param string $name The name of the naming to register.
     * @param NamingInterface $strategy The naming to register.
     * @return NamingEnabledInterface
     */
    public function addNaming($name, NamingInterface $strategy);

    /**
     * Gets the naming with the given name.
     *
     * @param string $name The name of the strategy to get.
     * @return NamingInterface
     */
    public function getNaming($name);

    /**
     * Checks if the naming with the given name exists.
     *
     * @param string $name The name of the strategy to check for.
     * @return bool
     */
    public function hasNaming($name);

    /**
     * Removes the naming with the given name.
     *
     * @param string $name The name of the naming to remove.
     * @return NamingEnabledInterface
     */
    public function removeNaming($name);
}