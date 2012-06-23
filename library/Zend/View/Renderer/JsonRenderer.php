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
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View\Renderer;

use JsonSerializable,
    Traversable,
    Zend\Json\Json,
    Zend\Stdlib\ArrayUtils,
    Zend\View\Exception,
    Zend\View\Model\ModelInterface as Model,
    Zend\View\Model\JsonModel,
    Zend\View\Renderer\RendererInterface as Renderer,
    Zend\View\Resolver\ResolverInterface as Resolver;

/**
 * JSON renderer
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class JsonRenderer implements Renderer, TreeRendererInterface
{
    /**
     * Whether or not to merge child models with no capture-to value set
     * @var bool
     */
    protected $mergeUnnamedChildren = false;

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * JSONP callback (if set, wraps the return in a function call)
     *
     * @var string
     */
    protected $jsonpCallback = null;

    /**
     * Return the template engine object, if any
     *
     * If using a third-party template engine, such as Smarty, patTemplate,
     * phplib, etc, return the template engine object. Useful for calling
     * methods on these objects, such as for setting filters, modifiers, etc.
     *
     * @return mixed
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * Set the resolver used to map a template name to a resource the renderer may consume.
     * 
     * @todo   Determine use case for resolvers when rendering JSON
     * @param  Resolver $resolver 
     * @return Renderer
     */
    public function setResolver(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Set flag indicating whether or not to merge unnamed children
     *
     * @param  bool $mergeUnnamedChildren
     * @return JsonRenderer
     */
    public function setMergeUnnamedChildren($mergeUnnamedChildren)
    {
        $this->mergeUnnamedChildren = (bool) $mergeUnnamedChildren;
        return $this;
    }

	/**
     * Set the JSONP callback function name
     *
     * @param  string $callback
     * @return JsonpModel
     */
    public function setJsonpCallback($callback)
    {
        $callback = (string) $callback;
        if (!empty($callback)) {
            $this->jsonpCallback = $callback;
        }
        return $this;
    }

    /**
     * Returns whether or not the jsonpCallback has been set
     *
     * @return bool
     */
    public function hasJsonpCallback()
    {
        return !is_null($this->jsonpCallback);
    }

    /**
     * Should we merge unnamed children?
     *
     * @return bool
     */
    public function mergeUnnamedChildren()
    {
        return $this->mergeUnnamedChildren;
    }

    /**
     * Renders values as JSON
     *
     * @todo   Determine what use case exists for accepting both $nameOrModel and $values
     * @param  string|Model $name The script/resource process, or a view model
     * @param  null|array|\ArrayAccess Values to use during rendering
     * @return string The script output.
     */
    public function render($nameOrModel, $values = null)
    {
        // use case 1: View Models
        // Serialize variables in view model
        if ($nameOrModel instanceof Model) {
            if ($nameOrModel instanceof JsonModel) {
                $values = $nameOrModel->serialize();
            } else {
                $values = $this->recurseModel($nameOrModel);
                $values = Json::encode($values);
            }

            if ($this->hasJsonpCallback()) {
                $values = $this->jsonpCallback.'('.$values.');';
            }
            return $values;
        }

        // use case 2: $nameOrModel is populated, $values is not
        // Serialize $nameOrModel
        if (null === $values) {
            if (!is_object($nameOrModel) || $nameOrModel instanceof JsonSerializable) {
                $return = Json::encode($nameOrModel);
            } elseif ($nameOrModel instanceof Traversable) {
                $nameOrModel = ArrayUtils::iteratorToArray($nameOrModel);
                $return = Json::encode($nameOrModel);
            } else {
                $return = Json::encode(get_object_vars($nameOrModel));
            }

            if ($this->hasJsonpCallback()) {
                $return = $this->jsonpCallback.'('.$return.');';
            }
            return $return;
        }

        // use case 3: Both $nameOrModel and $values are populated
        throw new Exception\DomainException(sprintf(
            '%s: Do not know how to handle operation when both $nameOrModel and $values are populated',
            __METHOD__
        ));
    }

    /**
     * Can this renderer render trees of view models?
     *
     * Yes.
     * 
     * @return true
     */
    public function canRenderTrees()
    {
        return true;
    }

    /**
     * Retrieve values from a model and recurse its children to build a data structure
     * 
     * @param  Model $model 
     * @return array
     */
    protected function recurseModel(Model $model)
    {
        $values = $model->getVariables();
        if ($values instanceof Traversable) {
            $values = ArrayUtils::iteratorToArray($values);
        }
        
        if (!$model->hasChildren()) {
            return $values;
        }

        $mergeChildren = $this->mergeUnnamedChildren();
        foreach ($model as $child) {
            $captureTo = $child->captureTo();
            if (!$captureTo && !$mergeChildren) {
                // We don't want to do anything with this child
                continue;
            }

            $childValues = $this->recurseModel($child);
            if ($captureTo) {
                // Capturing to a specific key
                $values[$captureTo] = $childValues;
            } elseif ($mergeChildren) {
                // Merging values with parent
                $values = array_replace_recursive($values, $childValues);
            }
        }
        return $values;
    }
}
