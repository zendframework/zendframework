<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Helper;

use Zend\View\Exception;

/**
 * Helper for rendering a template fragment in its own variable scope.
 *
 * @package    Zend_View
 * @subpackage Helper
 */
class Partial extends AbstractHelper
{
    /**
     * Variable to which object will be assigned
     * @var string
     */
    protected $objectKey;

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
     * @param  array $model Variables to populate in the view
     * @return string|Partial
     * @throws Exception\RuntimeException
     */
    public function __invoke($name = null, $model = null)
    {
        if (0 == func_num_args()) {
            return $this;
        }

        $view = $this->cloneView();
        if (isset($this->partialCounter)) {
            $view->partialCounter = $this->partialCounter;
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
     * @return \Zend\View\Renderer\RendererInterface
     */
    public function cloneView()
    {
        $view = clone $this->view;
        $view->setVars(array());
        return $view;
    }

    /**
     * Set object key
     *
     * @param  string $key
     * @return Partial
     */
    public function setObjectKey($key)
    {
        if (null === $key) {
            $this->objectKey = null;
        } else {
            $this->objectKey = (string) $key;
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
        return $this->objectKey;
    }
}
