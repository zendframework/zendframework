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
 * @package    Zend_Dojo
 * @subpackage Form_Element
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Dojo\Form\Element;
use Zend\Form,
    Zend\Form\Element\Exception;;

/**
 * DateTextBox dijit
 *
 * @uses       \Zend\Dojo\Form\Element\ValidationTextBox
 * @uses       \Zend\Form\Element\Exception
 * @package    Zend_Dojo
 * @subpackage Form_Element
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DateTextBox extends ValidationTextBox
{
    /**
     * Use DateTextBox dijit view helper
     * @var string
     */
    public $helper = 'DateTextBox';

    /**
     * Allowed formatLength types
     * @var array
     */
    protected $_allowedFormatTypes = array(
        'long',
        'short',
        'medium',
        'full',
    );

    /**
     * Allowed selector types
     * @var array
     */
    protected $_allowedSelectorTypes = array(
        'time',
        'date',
    );

    /**
     * Set am,pm flag
     *
     * @param  bool $am,pm
     * @return \Zend\Dojo\Form\Element\DateTextBox
     */
    public function setAmPm($flag)
    {
        $this->setConstraint('am,pm', (bool) $flag);
        return $this;
    }

    /**
     * Retrieve am,pm flag
     *
     * @return bool
     */
    public function getAmPm()
    {
        if (!$this->hasConstraint('am,pm')) {
            return false;
        }
        return ('true' ==$this->getConstraint('am,pm'));
    }

    /**
     * Set strict flag
     *
     * @param  bool $strict
     * @return \Zend\Dojo\Form\Element\DateTextBox
     */
    public function setStrict($flag)
    {
        $this->setConstraint('strict', (bool) $flag);
        return $this;
    }

    /**
     * Retrieve strict flag
     *
     * @return bool
     */
    public function getStrict()
    {
        if (!$this->hasConstraint('strict')) {
            return false;
        }
        return ('true' == $this->getConstraint('strict'));
    }

    /**
     * Set locale
     *
     * @param  string $locale
     * @return \Zend\Dojo\Form\Element\DateTextBox
     */
    public function setLocale($locale)
    {
        $this->setConstraint('locale', (string) $locale);
        return $this;
    }

    /**
     * Retrieve locale
     *
     * @return string|null
     */
    public function getLocale()
    {
        return $this->getConstraint('locale');
    }

    /**
     * Set date format pattern
     *
     * @param  string $pattern
     * @return \Zend\Dojo\Form\Element\NumberTextBox
     */
    public function setDatePattern($pattern)
    {
        $this->setConstraint('datePattern', (string) $pattern);
        return $this;
    }

    /**
     * Retrieve date format pattern
     *
     * @return string|null
     */
    public function getDatePattern()
    {
        return $this->getConstraint('datePattern');
    }

    /**
     * Set numeric format formatLength
     *
     * @see    $_allowedFormatTypes
     * @param  string $formatLength
     * @return \Zend\Dojo\Form\Element\NumberTextBox
     */
    public function setFormatLength($formatLength)
    {
        $formatLength = strtolower($formatLength);
        if (!in_array($formatLength, $this->_allowedFormatTypes)) {
            throw new Exception\InvalidArgumentException(sprintf('Invalid formatLength "%s" specified', $formatLength));
        }

        $this->setConstraint('formatLength', $formatLength);
        return $this;
    }

    /**
     * Retrieve formatLength
     *
     * @return string|null
     */
    public function getFormatLength()
    {
        return $this->getConstraint('formatLength');
    }

    /**
     * Set numeric format Selector
     *
     * @see    $_allowedSelectorTypes
     * @param  string $selector
     * @return \Zend\Dojo\Form\Element\NumberTextBox
     */
    public function setSelector($selector)
    {
        $selector = strtolower($selector);
        if (!in_array($selector, $this->_allowedSelectorTypes)) {
            throw new Exception\InvalidArgumentException(sprintf('Invalid Selector "%s" specified', $selector));
        }

        $this->setConstraint('selector', $selector);
        return $this;
    }

    /**
     * Retrieve selector
     *
     * @return string|null
     */
    public function getSelector()
    {
        return $this->getConstraint('selector');
    }
}
