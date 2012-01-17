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
    Zend\Stdlib\IteratorToArray,
    Zend\View\Exception,
    Zend\View\Model,
    Zend\View\Renderer,
    Zend\View\Resolver;

/**
 * Interface class for Zend_View compatible template engine implementations
 *
 * @todo       Should this use Zend\Json?
 * @category   Zend
 * @package    Zend_View
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class JsonRenderer implements Renderer
{
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
            if ($nameOrModel instanceof Model\JsonModel) {
                $values = $nameOrModel->serialize();
            } else {
                $values = $nameOrModel->getVariables();
                $values = json_encode($values);
            }

            return $values;
        }

        // use case 2: $nameOrModel is populated, $values is not
        // Serialize $nameOrModel
        if (null === $values) {
            if (!is_object($nameOrModel)) {
                return json_encode($nameOrModel);
            }

            if ($nameOrModel instanceof JsonSerializable) {
                return $nameOrModel->jsonSerialize();
            }

            if ($nameOrModel instanceof Traversable) {
                $nameOrModel = IteratorToArray::convert($nameOrModel);
                return json_encode($nameOrModel);
            }

            return json_encode(get_object_vars($nameOrModel));
        }

        // use case 3: Both $nameOrModel and $values are populated
        throw new Exception\DomainException(sprintf(
            '%s: Do not know how to handle operation when both $nameOrModel and $values are populated',
            __METHOD__
        ));
    }
}

