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
use Zend\Stdlib\Hydrator;

/**
 * @category   Zend
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface FormInterface extends FieldsetInterface
{
    const BIND_ON_VALIDATE  = 0x00;
    const BIND_MANUAL       = 0x01;
    const VALIDATE_ALL      = 0x10;
    const VALUES_NORMALIZED = 0x11;
    const VALUES_RAW        = 0x12;
    const VALUES_AS_ARRAY   = 0x13;
 
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
     * Bind an object to the form
     *
     * Allows populating the object with validated values.
     * 
     * @param  object $object 
     * @return void
     */
    public function bind($object);

    /**
     * Bind values to the bound object
     * 
     * @return void
     */
    public function bindValues();

    /**
     * Whether or not to bind values to the bound object when validation succeeds
     * 
     * @param  int $bindOnValidateFlag 
     * @return void
     */
    public function setBindOnValidate($bindOnValidateFlag);

    /**
     * Set input filter
     * 
     * @param  InputFilterInterface $inputFilter 
     * @return InputFilterAwareInterface
     */
    public function setInputFilter(InputFilterInterface $inputFilter);

    /**
     * Retrieve input filter
     * 
     * @return InputFilterInterface
     */
    public function getInputFilter();

    /**
     * Set the hydrator to use when binding an object to the form
     * 
     * @param  Hydrator\HydratorInterface $hydrator 
     * @return FormInterface
     */
    public function setHydrator(Hydrator\HydratorInterface $hydrator);

    /**
     * Get the hydrator used when binding an object to the form
     * 
     * @return null|Hydrator\HydratorInterface
     */
    public function getHydrator();

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
     * Set the validation group (set of values to validate)
     *
     * Typically, proxies to the composed input filter
     *
     * @return FormInterface
     */
    public function setValidationGroup();
}
