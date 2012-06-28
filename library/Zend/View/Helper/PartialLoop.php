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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
