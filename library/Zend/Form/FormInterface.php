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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form;

use IteratorAggregate;
use Zend\InputFilter\InputFilterInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface FormInterface extends FieldsetInterface
{
    const VALIDATE_ALL      = 1;
    const VALUES_NORMALIZED = 2;
    const VALUES_RAW        = 3;
    const VALUES_AS_ARRAY   = 4;
 
    /**
     * Set data to validate and/or populate elements
     *
     * Typically, also passes data on to the composed input filter.
     * 
     * @param  array|\ArrayAccess $data 
     * @return FormInterface
     */
    public function setData($data);

    /**
     * Bind a model to the form
     *
     * Ensures the model is populated with validated values.
     * 
     * @param  object $model 
     * @return void
     */
    public function bindModel(object $model);

    /**
     * Validate the form
     *
     * Typically, will proxy to the composed input filter.
     * 
     * @return bool
     */
    public function isValid();

    /**
     * Retrieve the validated data
     *
     * By default, retrieves normalized values; pass one of the VALUES_* 
     * constants to shape the behavior.
     * 
     * @param  int $flag 
     * @return array|object
     */
    public function getData($flag = FormInterface::VALUES_NORMALIZED);

    /**
     * Set the input filter used to validate this form
     * 
     * @param  InputFilterInterface $inputFilter
     * @return FormInterface
     */
    public function setInputFilter(InputFilterInterface $inputFilter);

    /**
     * Get the composed input filter
     * 
     * @return InputFilterInterface
     */
    public function getInputFilter();

    /**
     * Set the validation group (set of values to validate)
     *
     * Typically, proxies to the composed input filter
     *
     * @return FormInterface
     */
    public function setValidationGroup();
}
