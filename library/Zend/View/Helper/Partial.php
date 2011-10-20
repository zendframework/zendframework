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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View\Helper;

/**
 * Helper for rendering a template fragment in its own variable scope.
 *
 * @uses       \Zend\Controller\Front
 * @uses       \Zend\View\Helper\AbstractHelper
 * @uses       \Zend\View\Helper\Partial\Exception
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Partial extends AbstractHelper
{
    /**
     * Variable to which object will be assigned
     * @var string
     */
    protected $_objectKey;

    /**
     * Renders a template fragment within a variable scope distinct from the
     * calling View object.
     *
     * If no arguments are passed, returns the helper instance.
     *
     * If the $model is an array, it is passed to the view object's assign()
     * method.
     *
     * If the $model is an object, it first checks to see if the object
     * implements a 'toArray' method; if so, it passes the result of that
     * method to to the view object's assign() method. Otherwise, the result of
     * get_object_vars() is passed.
     *
     * @param  string $name Name of view script
     * @param  string|array $module If $model is empty, and $module is an array,
     *                              these are the variables to populate in the
     *                              view. Otherwise, the module in which the
     *                              partial resides
     * @param  array $model Variables to populate in the view
     * @return string|\Zend\View\Helper\Partial\Partial
     */
    public function __invoke($name = null, $module = null, $model = null)
    {
        if (0 == func_num_args()) {
            return $this;
        }

        $view = $this->cloneView();
        if (isset($this->partialCounter)) {
            $view->partialCounter = $this->partialCounter;
        }
        if ((null !== $module) && is_string($module)) {
            $moduleDir = \Zend\Controller\Front::getInstance()->getControllerDirectory($module);
            if (null === $moduleDir) {
                $e = new Partial\Exception('Cannot render partial; module does not exist');
                $e->setView($this->view);
                throw $e;
            }
            $viewsDir = dirname($moduleDir) . '/views';
            $view->resolver()->addPath($viewsDir . '/scripts');
        } elseif ((null == $model) && (null !== $module)
            && (is_array($module) || is_object($module)))
        {
            $model = $module;
        }

        if (!empty($model)) {
            if (is_array($model)) {
                $view->vars()->assign($model);
            } elseif (is_object($model)) {
                if (null !== ($objectKey = $this->getObjectKey())) {
                    $view->vars()->offsetSet($objectKey, $model);
                } elseif (method_exists($model, 'toArray')) {
                    $view->vars()->assign($model->toArray());
                } else {
                    $view->vars()->assign(get_object_vars($model));
                }
            }
        }

        return $view->render($name);
    }

    /**
     * Clone the current View
     *
     * @return \Zend\View\Renderer
     */
    public function cloneView()
    {
        $view = clone $this->view;
        $view->vars()->clear();
        return $view;
    }

    /**
     * Set object key
     *
     * @param  string $key
     * @return \Zend\View\Helper\Partial\Partial
     */
    public function setObjectKey($key)
    {
        if (null === $key) {
            $this->_objectKey = null;
        } else {
            $this->_objectKey = (string) $key;
        }

        return $this;
    }

    /**
     * Retrieve object key
     *
     * The objectKey is the variable to which an object in the iterator will be
     * assigned.
     *
     * @return null|string
     */
    public function getObjectKey()
    {
        return $this->_objectKey;
    }
}
