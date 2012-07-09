<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace Zend\Dojo\Form\Element;

use Zend\Form\Element\Exception;

/**
 * TimeTextBox dijit
 *
 * @package    Zend_Dojo
 * @subpackage Form_Element
 */
class TimeTextBox extends DateTextBox
{
    /**
     * Use TimeTextBox dijit view helper
     * @var string
     */
    public $helper = 'TimeTextBox';

    /**
     * Validate ISO 8601 time format
     *
     * @param  string $format
     * @return true
     * @throws \Zend\Form\Element\Exception
     */
    protected function _validateIso8601($format)
    {
        if (!preg_match('/^T\d{2}:\d{2}:\d{2}$/', $format)) {
            throw new Exception\InvalidArgumentException(sprintf('Invalid format "%s" provided; must match T:00:00:00 format', $format));
        }
        return true;
    }

    /**
     * Set time format pattern
     *
     * @param  string $pattern
     * @return \Zend\Dojo\Form\Element\NumberTextBox
     */
    public function setTimePattern($pattern)
    {
        $this->setConstraint('timePattern', (string) $pattern);
        return $this;
    }

    /**
     * Retrieve time format pattern
     *
     * @return string|null
     */
    public function getTimePattern()
    {
        return $this->getConstraint('timePattern');
    }

    /**
     * Set clickableIncrement
     *
     * @param  string $format
     * @return \Zend\Dojo\Form\Element\NumberTextBox
     */
    public function setClickableIncrement($format)
    {
        $format = (string) $format;
        $this->_validateIso8601($format);
        $this->setConstraint('clickableIncrement', $format);
        return $this;
    }

    /**
     * Retrieve clickableIncrement
     *
     * @return string|null
     */
    public function getClickableIncrement()
    {
        return $this->getConstraint('clickableIncrement');
    }

    /**
     * Set visibleIncrement
     *
     * @param  string $format
     * @return \Zend\Dojo\Form\Element\NumberTextBox
     */
    public function setVisibleIncrement($format)
    {
        $format = (string) $format;
        $this->_validateIso8601($format);
        $this->setConstraint('visibleIncrement', $format);
        return $this;
    }

    /**
     * Retrieve visibleIncrement
     *
     * @return string|null
     */
    public function getVisibleIncrement()
    {
        return $this->getConstraint('visibleIncrement');
    }

    /**
     * Set visibleRange
     *
     * @param  string $format
     * @return \Zend\Dojo\Form\Element\NumberTextBox
     */
    public function setVisibleRange($format)
    {
        $format = (string) $format;
        $this->_validateIso8601($format);
        $this->setConstraint('visibleRange', $format);
        return $this;
    }

    /**
     * Retrieve visibleRange
     *
     * @return string|null
     */
    public function getVisibleRange()
    {
        return $this->getConstraint('visibleRange');
    }
}
