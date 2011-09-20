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
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Router\Route;

use Zend\Controller\Router\Route;

/**
 * Abstract Route
 *
 * Implements interface and provides convenience methods
 *
 * @uses       \Zend\Controller\Router\Route
 * @uses       \Zend\Controller\Router\Route\Chain
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractRoute implements Route
{
    /**
     * Wether this route is abstract or not
     *
     * @var boolean
     */
    protected $_isAbstract = false;

    /**
     * Path matched by this route
     *
     * @var string
     */
    protected $_matchedPath = null;

    /**
     * Get the version of the route
     *
     * @return integer
     */
    public function getVersion()
    {
        return 2;
    }

    /**
     * Set partially matched path
     *
     * @param  string $path
     * @return void
     */
    public function setMatchedPath($path)
    {
        $this->_matchedPath = $path;
    }

    /**
     * Get partially matched path
     *
     * @return string
     */
    public function getMatchedPath()
    {
        return $this->_matchedPath;
    }

    /**
     * Check or set wether this is an abstract route or not
     *
     * @param  boolean $flag
     * @return boolean
     */
    public function isAbstract($flag = null)
    {
        if ($flag !== null) {
            $this->_isAbstract = $flag;
        }

        return $this->_isAbstract;
    }

    /**
     * Create a new chain
     *
     * @param  \Zend\Controller\Router\Route\AbstractRoute $route
     * @param  string                                $separator
     * @return \Zend\Controller\Router\Route\Chain
     */
    public function addChain(AbstractRoute $route, $separator = '/')
    {
        $chain = new Chain();
        $chain->addChain($this)->addChain($route, $separator);
        return $chain;
    }
}
