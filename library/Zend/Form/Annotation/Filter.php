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
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\Annotation;

/**
 * Filter annotation
 *
 * Expects an associative array defining the filter.  Typically, this includes 
 * the "name" with an associated string value indicating the filter name or 
 * class, and optionally an "options" key with an object/associative array value
 * of options to pass to the filter constructor.
 *
 * This annotation may be specified multiple times; filters will be added
 * to the filter chain in the order specified.
 *
 * @Annotation
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Filter extends AbstractArrayAnnotation
{
    /**
     * Retrieve the filter specification
     * 
     * @return null|array
     */
    public function getFilter()
    {
        return $this->value;
    }
}
