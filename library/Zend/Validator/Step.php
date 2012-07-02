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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Validator;

use Traversable;

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Step extends AbstractValidator
{
    const INVALID = 'typeInvalid';
    const NOT_STEP = 'stepInvalid';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "Invalid value given. Scalar expected.",
        self::NOT_STEP => "The input is not a valid step."
    );

    /**
     * @var mixed
     */
    protected $baseValue = 0;

    /**
     * @var mixed
     */
    protected $step = 1;

    /**
     * Set default options for this instance
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = iterator_to_array($options);
        } elseif (!is_array($options)) {
            $options = func_get_args();
            $temp['baseValue'] = array_shift($options);
            if (!empty($options)) {
                $temp['step'] = array_shift($options);
            }

            $options = $temp;
        }

        if (isset($options['baseValue'])) {
            $this->setBaseValue($options['baseValue']);
        }
        if (isset($options['step'])) {
            $this->setStep($options['step']);
        }

        parent::__construct($options);
    }

    /**
     * Sets the base value from which the step should be computed
     *
     * @param mixed $baseValue
     * @return Step
     */
    public function setBaseValue($baseValue)
    {
        $this->baseValue = $baseValue;
        return $this;
    }

    /**
     * Returns the base value from which the step should be computed
     *
     * @return string
     */
    public function getBaseValue()
    {
        return $this->baseValue;
    }

    /**
     * Sets the step value
     *
     * @param mixed $step
     * @return Step
     */
    public function setStep($step)
    {
        $this->step = $step;
        return $this;
    }

    /**
     * Returns the step value
     *
     * @return string
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Returns true if $value is a scalar and a valid step value
     *
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        if (!is_numeric($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);

        if (fmod($value - $this->baseValue, $this->step) !== 0.0) {
            $this->error(self::NOT_STEP);
            return false;
        }

        return true;
    }
}
