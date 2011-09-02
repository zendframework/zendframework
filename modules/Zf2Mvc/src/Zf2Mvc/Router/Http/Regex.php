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
 * @package    Zend_Router
 * @subpackage Route
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Router\Http;

use Zend\Stdlib\RequestDescription as Request,
    Zf2Mvc\Router\Route,
    Zf2Mvc\Router\RouteMatch;

/**
 * Regex route.
 *
 * @package    Zend_Router
 * @subpackage Route
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Regex implements Route
{
    /**
     * Regex to match
     * 
     * @var string
     */
    protected $_regex;

    /**
     * Default values
     *
     * @var array
     */
    protected $_defaults;

    /**
     * __construct(): defined by Route interface.
     *
     * @see    Route::__construct()
     * @param  mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
    }

    /**
     * match(): defined by Route interface.
     *
     * @see    Route::match()
     * @param  Request $request
     * @return RouteMatch
     */
    public function match(Request $request, $pathOffset = null)
    {
        if (!method_exists($request, 'getRequestUri')) {
            return null;
        }

        if ($pathOffset !== null) {
            $result = preg_match('(\G' . $this->_regex . ')i', $request->getRequestUri(), $match, null, $pathOffset);
        } else {
            $result = preg_match('(^' . $this->_regex . '$)i', $request->getRequestUri(), $match);
        }

        if ($result === null) {
            return null;
        }

        // @todo: examine $match
        return $this->_defaults;
    }

    /**
     * assemble(): Defined by Route interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = null, array $options = null)
    {
        // @todo: implement this
    }
}
