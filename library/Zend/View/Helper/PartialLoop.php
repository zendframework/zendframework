<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Helper;

use Iterator;
use Traversable;
use Zend\Stdlib\ArrayUtils;
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
     *
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
     * @param  array $values Variables to populate in the view
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function __invoke($name = null, $values = null)
    {
        if (0 == func_num_args()) {
            return $this;
        }

        if (!is_array($values)
            && (!$values instanceof Traversable)
            && (is_object($values) && !method_exists($values, 'toArray'))
        ) {
            throw new Exception\InvalidArgumentException('PartialLoop helper requires iterable data');
        }

        if (is_object($values)
            && (!$values instanceof Traversable)
            && method_exists($values, 'toArray')
        ) {
            $values = $values->toArray();
        }

        if ($values instanceof Iterator) {
            $values = ArrayUtils::iteratorToArray($values);
        }

        // reset the counter if it's called again
        $this->partialCounter = 0;
        $content = '';

        foreach ($values as $item) {
            $this->partialCounter++;
            $content .= parent::__invoke($name, $item);
        }

        return $content;
    }

    /**
     * Get the partial counter
     *
     * @return int
     */
    public function getPartialCounter()
    {
        return $this->partialCounter;
    }
}
