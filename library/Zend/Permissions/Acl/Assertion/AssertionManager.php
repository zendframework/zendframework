<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Zend\Permissions\Acl\Assertion;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\Permissions\Acl\Exception\InvalidArgumentException;

class AssertionManager extends AbstractPluginManager
{

    protected $sharedByDefault = true;

    /**
     * Validate the plugin
     *
     * Checks that the element is an instance of AssertionInterface
     *
     * @param mixed $plugin
     *
     * @throws InvalidArgumentException
     * @return bool
     */
    public function validatePlugin($plugin)
    {
        if (! $plugin instanceof AssertionInterface) {
            throw new InvalidArgumentException(sprintf('Plugin of type %s is invalid; must implement
                Zend\Permissions\Acl\Assertion\AssertionInterface',
                (is_object($plugin) ? get_class($plugin) : gettype($plugin))));
        }

        return true;
    }
}
