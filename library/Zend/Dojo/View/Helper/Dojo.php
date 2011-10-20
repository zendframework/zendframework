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
 * @package    Zend_Dojo
 * @subpackage View
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Dojo\View\Helper;

use Zend\Dojo\View\Exception,
    Zend\Registry,
    Zend\View\Renderer as View,
    Zend\View\Helper\AbstractHelper as AbstractViewHelper;

/**
 * Zend_Dojo_View_Helper_Dojo: Dojo View Helper
 *
 * Allows specifying stylesheets, path to dojo, module paths, and onLoad
 * events.
 *
 * @uses       \Zend\Dojo\View\Exception
 * @uses       \Zend\Dojo\View\Helper\DojoContainer
 * @uses       \Zend\Registry
 * @package    Zend_Dojo
 * @subpackage View
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dojo extends AbstractViewHelper
{
    /**#@+
     * Programmatic dijit creation style constants
     */
    const PROGRAMMATIC_SCRIPT = 1;
    const PROGRAMMATIC_NOSCRIPT = -1;
    /**#@-*/

    /**
     * @var \Zend\View\Renderer
     */
    public $view;

    /**
     * @var \Zend\Dojo\View\Helper\Dojo\Container
     */
    protected $_container;

    /**
     * @var bool Whether or not dijits should be declared programmatically
     */
    protected static $_useProgrammatic = true;

    /**
     * Initialize helper
     *
     * Retrieve container from registry or create new container and store in
     * registry.
     *
     * @return void
     */
    public function __construct()
    {
        $registry = Registry::getInstance();
        $key      = __CLASS__;
        if (!isset($registry[$key])) {
            $container = new Dojo\Container();
            $registry[$key] = $container;
        }
        $this->_container = $registry[$key];
    }

    /**
     * Set view object
     *
     * @param  Zend\View\Renderer $view
     * @return void
     */
    public function setView(View $view)
    {
        $this->view = $view;
        $this->_container->setView($view);
    }

    /**
     * Return dojo container
     *
     * @return \Zend\Dojo\View\Helper\Dojo\Container
     */
    public function __invoke()
    {
        return $this->_container;
    }

    /**
     * Proxy to container methods
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws \Zend\Dojo\View\Exception For invalid method calls
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->_container, $method)) {
            throw new Exception\BadMethodCallException(sprintf('Invalid method "%s" called on dojo view helper', $method));
        }

        return call_user_func_array(array($this->_container, $method), $args);
    }

    /**
     * Set whether or not dijits should be created declaratively
     *
     * @return void
     */
    public static function setUseDeclarative()
    {
        self::$_useProgrammatic = false;
    }

    /**
     * Set whether or not dijits should be created programmatically
     *
     * Optionally, specifiy whether or not dijit helpers should generate the
     * programmatic dojo.
     *
     * @param  int $style
     * @return void
     */
    public static function setUseProgrammatic($style = self::PROGRAMMATIC_SCRIPT)
    {
        if (!in_array($style, array(self::PROGRAMMATIC_SCRIPT, self::PROGRAMMATIC_NOSCRIPT))) {
            $style = self::PROGRAMMATIC_SCRIPT;
        }
        self::$_useProgrammatic = $style;
    }

    /**
     * Should dijits be created declaratively?
     *
     * @return bool
     */
    public static function useDeclarative()
    {
        return (false === self::$_useProgrammatic);
    }

    /**
     * Should dijits be created programmatically?
     *
     * @return bool
     */
    public static function useProgrammatic()
    {
        return (false !== self::$_useProgrammatic);
    }

    /**
     * Should dijits be created programmatically but without scripts?
     *
     * @return bool
     */
    public static function useProgrammaticNoScript()
    {
        return (self::PROGRAMMATIC_NOSCRIPT === self::$_useProgrammatic);
    }
}
