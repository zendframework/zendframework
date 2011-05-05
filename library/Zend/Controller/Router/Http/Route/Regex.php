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
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Router\Http\Route;
use Zend\Controller\Router\Route,
    Zend\Controller\Request\AbstractRequest,
    Zend\Controller\Request\Http as HttpRequest;

/**
 * Regex route.
 *
 * @package    Zend_Controller
 * @subpackage Router
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
     * @param  array $options
     * @return void
     */
    public function __construct(array $options = null)
    {
    }

    /**
     * match(): defined by Route interface.
     *
     * @see    Route::match()
     * @param  AbstractRequest $request
     * @return RouteMatch
     */
    public function match(AbstractRequest $request, $pathOffset = null)
    {
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
