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
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Config\Processor;

use Zend\Config\Config,
    Zend\Config\Processor,
    Zend\Config\Processor\Token,
    Zend\Config\Exception\InvalidArgumentException,
    \Traversable,
    \ArrayObject;

/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Constant extends Token implements Processor
{
    /**
     * Replace only user-defined tokens
     *
     * @var bool
     */
    protected $userOnly = true;

    /**
     * Constant Processor walks through a Config structure and replaces all
     * PHP constants with their respective values
     *
     * @param bool   $userOnly              True to process only user-defined constants, false to process all PHP constants
     * @param string $prefix                Optional prefix
     * @param string $suffix                Optional suffix
     * @return \Zend\Config\Processor\Constant
     */
    public function __construct($userOnly = true, $prefix = '', $suffix = '')
    {
        $this->setUserOnly($userOnly);
        $this->setPrefix($prefix);
        $this->setSuffix($suffix);

        $this->loadConstants();
    }

    /**
     * @return bool
     */
    public function getUserOnly()
    {
        return $this->userOnly;
    }

    /**
     * Should we use only user-defined constants?
     *
     * @param $userOnly
     * @return bool
     */
    public function setUserOnly($userOnly)
    {
        return $this->userOnly = $userOnly;
    }

    /**
     * Load all currently defined constants into parser.
     *
     * @return void
     */
    public function loadConstants()
    {
        if ($this->userOnly) {
            $constants = get_defined_constants(true);
            $constants = isset($constants['user']) ? $constants['user'] : array();
            $this->setTokens($constants);
        } else {
            $this->setTokens(get_defined_constants());
        }
    }

    /**
     * Get current token registry.
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

}
