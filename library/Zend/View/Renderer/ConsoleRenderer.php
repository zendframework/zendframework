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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View\Renderer;

use ArrayAccess;
use Zend\Filter\FilterChain;
use Zend\Loader\Pluggable;
use Zend\View\Exception;
use Zend\View\HelperBroker;
use Zend\View\Model;
use Zend\View\Renderer;
use Zend\View\Resolver;
use Zend\View\Variables;

/**
 * Abstract class for Zend_View to help enforce private constructs.
 *
 * Note: all private variables in this class are prefixed with "__". This is to
 * mark them as part of the internal implementation, and thus prevent conflict
 * with variables injected into the renderer.
 *
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ConsoleRenderer implements Renderer, TreeRendererInterface
{
    /**
     * @var Zend\Filter\FilterChain
     * @protected
     */
    protected $__filterChain;

    /**
     * Constructor.
     *
     *
     * @todo handle passing helper broker, options
     * @todo handle passing filter chain, options
     * @todo handle passing variables object, options
     * @todo handle passing resolver object, options
     * @param array $config Configuration key-value pairs.
     */
    public function __construct($config = array())
    {
        $this->init();
    }

    public function setResolver(Resolver $resolver){}

    /**
     * Return the template engine object
     *
     * Returns the object instance, as it is its own template engine
     *
     * @return PhpRenderer
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * Allow custom object initialization when extending Zend_View_Abstract or
     * Zend_View
     *
     * Triggered by {@link __construct() the constructor} as its final action.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Set filter chain
     *
     * @param  FilterChain $filters
     * @return Zend\View\PhpRenderer
     */
    public function setFilterChain(FilterChain $filters)
    {
        $this->__filterChain = $filters;
        return $this;
    }

    /**
     * Retrieve filter chain for post-filtering script content
     *
     * @return FilterChain
     */
    public function getFilterChain()
    {
        if (null === $this->__filterChain) {
            $this->setFilterChain(new FilterChain());
        }
        return $this->__filterChain;
    }

    /**
     * Recursively processes all ViewModels and returns output.
     *
     * @param  string|Model            $model        A ViewModel instance.
     * @param  null|array|Traversable  $values       Values to use when rendering. If none
     *                                               provided, uses those in the composed
     *                                               variables container.
     * @return string Console output.
     */
    public function render($model, $values = null)
    {
        if(!$model instanceof Model){
            return '';
        }

        $result = '';
        $options = $model->getOptions();
        foreach ($options as $setting => $value) {
            $method = 'set' . $setting;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
            unset($method, $setting, $value);
        }
        unset($options);

        $values = $model->getVariables();

        if(isset($values['result'])){
            // filter and append the result
            $result .= $this->getFilterChain()->filter($values['result']);
        }

        if($model->hasChildren()){
            // recursively render all children
            foreach($model->getChildren() as $child){
                $result .= $this->render($child, $values);
            }
        }

        return $result;
    }

    /**
     * @see Zend\View\Renderer\TreeRendererInterface
     * @return bool
     */
    public function canRenderTrees()
    {
        return true;
    }

}
