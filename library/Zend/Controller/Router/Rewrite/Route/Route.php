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
namespace Zend\Controller\Router\Rewrite\Route;
use Zend\Controller\Request\Http as HttpRequest;

/**
 * Route interface
 *
 * @package    Zend_Controller
 * @subpackage Router
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
interface Route
{
    /**
     * Create a new route
     * 
     * @param  array $options
     * @return void
     */
    //public function __construct(array $options);

    /**
     * Match a request
     *
     * @param  HttpRequest $request
     * @param  integer     $pathOffset
     * @return boolean
     */
    public function match(HttpRequest $request, $pathOffset = null);

    /**
     * Assemble an URL
     *
     * @param  array $params
     * @param  array $options
     * @return string
     */
    public function assemble(array $params = null, array $options = null);
}
