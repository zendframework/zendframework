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

use Traversable;

/**
 * Helper for rendering a template fragment in its own variable scope; iterates
 * over data provided and renders for each iteration.
 *
 * @uses       \Zend\View\Helper\Partial\Partial
 * @uses       \Zend\View\Helper\Partial\Exception
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PartialLoop extends Partial
{

    /**
     * Marker to where the pointer is at in the loop
     * @var integer
     */
    protected $partialCounter = 0;

    /**
     * Renders a template fragment within a variable scope distinct from the
     * calling View object.
     *
     * If no arguments are provided, returns object instance.
     *
     * @param  string $name Name of view script
     * @param  string|array $module If $model is empty, and $module is an array,
     *                              these are the variables to populate in the
     *                              view. Otherwise, the module in which the
     *                              partial resides
     * @param  array $model Variables to populate in the view
     * @return string
     */
    public function __invoke($name = null, $module = null, $model = null)
    {
        if (0 == func_num_args()) {
            return $this;
        }

        if ((null === $model) && (null !== $module)) {
            $model  = $module;
            $module = null;
        }

        if (!is_array($model)
            && (!$model instanceof Traversable)
            && (is_object($model) && !method_exists($model, 'toArray'))
        ) {
            $e = new Partial\Exception('PartialLoop helper requires iterable data');
            $e->setView($this->view);
            throw $e;
        }

        if (is_object($model)
            && (!$model instanceof Traversable)
            && method_exists($model, 'toArray')
        ) {
            $model = $model->toArray();
        }

        $content = '';
        // reset the counter if it's called again
        $this->partialCounter = 0;
        foreach ($model as $item) {
            // increment the counter variable
            $this->partialCounter++;

            $content .= parent::__invoke($name, $module, $item);
        }

        return $content;
    }
}
