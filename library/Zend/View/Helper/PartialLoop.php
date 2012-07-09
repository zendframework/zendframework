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

use Traversable;
use Zend\View\Exception;

/**
 * Helper for rendering a template fragment in its own variable scope; iterates
 * over data provided and renders for each iteration.
 *
 * @package    Zend_View
 * @subpackage Helper
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
     * @param  array $model Variables to populate in the view
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function __invoke($name = null, $model = null)
    {
        if (0 == func_num_args()) {
            return $this;
        }

        if (!is_array($model)
            && (!$model instanceof Traversable)
            && (is_object($model) && !method_exists($model, 'toArray'))
        ) {
            throw new Exception\InvalidArgumentException('PartialLoop helper requires iterable data');
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

            $content .= parent::__invoke($name, $item);
        }

        return $content;
    }
}
