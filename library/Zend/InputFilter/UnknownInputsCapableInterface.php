<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InputFilter
 */

namespace Zend\InputFilter;

/**
 * Implementors of this interface may report on the existence of unknown input,
 * as well as retrieve all unknown values.
 *
 * @category   Zend
 * @package    Zend_InputFilter
 */
interface UnknownInputsCapableInterface
{
    public function hasUnknown();
    public function getUnknown();
}
