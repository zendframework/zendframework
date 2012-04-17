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
class Form extends Fieldset implements FormInterface
{
    /**
     * Seed attributes
     * 
     * @var array
     */
    protected $attributes = array(
        'method' => 'POST',
    );
 
    /**
     * @var null|InputFilterInterface
     */
    protected $filter;

    /**
     * Hydrator to use with bound model
     * 
     * @var Hydrator\HydratorInterface
     */
    protected $hydrator;

    /**
     * The model bound to this form, if any
     * 
     * @var null|object
     */
    protected $model;

    /**
     * Validation group, if any
     * 
     * @var null|array
     */
    protected $validationGroup;

    /**
     * Set data to validate and/or populate elements
     *
     * Typically, also passes data on to the composed input filter.
     * 
     * @param  array|\ArrayAccess $data 
     * @return FormInterface
     */
    public function setData($data)
    {
    }

    /**
     * Bind a model to the form
     *
     * Ensures the model is populated with validated values.
     * 
     * @param  object $model 
     * @return void
     */
    public function bind($model)
    {
        if (!is_object($model)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an object argument; received "%s"',
                __METHOD__,
                $model
            ));
        }
        $this->model = $model;
    }

    /**
     * Set the hydrator to use when binding an object to the form
     * 
     * @param  Hydrator\HydratorInterface $hydrator 
     * @return Form
     */
    public function setHydrator(Hydrator\HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
        return $this;
    }

    /**
     * Get the hydrator used when binding an object to the form
     * 
     * @return null|Hydrator\HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator instanceof Hydrator\HydratorInterface) {
            $this->setHydrator(new Hydrator\ArraySerializable());
        }
        return $this->hydrator;
    }

    /**
     * Validate the form
     *
     * Typically, will proxy to the composed input filter.
     * 
     * @return bool
     */
    public function isValid()
    {
    }

    /**
     * Retrieve the validated data
     *
     * By default, retrieves normalized values; pass one of the 
     * FormInterface::VALUES_* constants to shape the behavior.
     * 
     * @param  int $flag 
     * @return array|object
     */
    public function getData($flag = FormInterface::VALUES_NORMALIZED)
    {
    }

    /**
     * Set the validation group (set of values to validate)
     *
     * Typically, proxies to the composed input filter
     *
     * @return FormInterface
     */
    public function setValidationGroup()
    {
        $argc = func_num_args();
        if (0 === $argc) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects at least one argument; none provided',
                __METHOD__
            ));
        }

        $argv = func_get_args();

        if (1 < $argc) {
            $this->validationGroup = $argv;
            return $this;
        }

        $arg = array_shift($argv);
        if ($arg === FormInterface::VALIDATE_ALL) {
            $this->validationGroup = null;
            return $this;
        }

        if (!is_array($arg)) {
            $arg = (array) $arg;
        }
        $this->validationGroup = $arg;
        return $this;
    }

    /**
     * Set the input filter used by this form
     * 
     * @param  InputFilterInterface $inputFilter 
     * @return Form
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->filter = $inputFilter;
        return $this;
    }

    /**
     * Retrive input filter used by this form
     * 
     * @return null|InputFilterInterface
     */
    public function getInputFilter()
    {
        return $this->filter;
    }
}
