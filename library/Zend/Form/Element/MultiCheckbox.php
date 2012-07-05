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
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\Element;

use Traversable;
use Zend\Form\Element;
use Zend\Form\Exception;
use Zend\InputFilter\InputProviderInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MultiCheckbox extends Element implements InputProviderInterface
{
    /**
     * @var bool
     */
    protected $useHiddenElement;

    /**
     * @var string
     */
    protected $uncheckedValue;

    /**
     * @var array
     */
    protected $labelAttributes;

    /**
     * Accepted options for MultiCheckbox:
     * - use_hidden_element: do we render hidden element?
     * - unchecked_value: value for checkbox when unchecked
     * - label_attributes: attributes to use for the label
     *
     * @param  array|\Traversable $options
     * @return MultiCheckbox
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['use_hidden_element'])) {
            $this->setUseHiddenElement($options['use_hidden_element']);
        }

        if (isset($options['unchecked_value'])) {
            $this->setUncheckedValue($options['unchecked_value']);
        }

        if (isset($options['label_attributes'])) {
            $this->setLabelAttributes($options['label_attributes']);
        }

        return $this;
    }

    /**
     * Do we render hidden element?
     *
     * @param  bool $useHiddenElement
     * @return MultiCheckbox
     */
    public function setUseHiddenElement($useHiddenElement)
    {
        $this->useHiddenElement = (bool)$useHiddenElement;
        return $this;
    }

    /**
     * Do we render hidden element?
     *
     * @return bool
     */
    public function useHiddenElement()
    {
        return $this->useHiddenElement;
    }

    /**
     * Set the value to use when checkbox is unchecked
     *
     * @param $uncheckedValue
     * @return MultiCheckbox
     */
    public function setUncheckedValue($uncheckedValue)
    {
        $this->uncheckedValue = $uncheckedValue;
        return $this;
    }

    /**
     * Get the value to use when checkbox is unchecked
     *
     * @return string
     */
    public function getUncheckedValue()
    {
        return $this->uncheckedValue;
    }

    /**
     * Set the label attributes
     *
     * @param  array $labelAttributes
     * @return MultiCheckbox
     */
    public function setLabelAttributes(array $labelAttributes)
    {
        $this->labelAttributes = $labelAttributes;
        return $this;
    }

    /**
     * Get the label attributes
     *
     * @return array
     */
    public function getLabelAttributes()
    {
        return $this->labelAttributes;
    }

    /**
     * Provide default input rules for this element
     *
     * Attaches the captcha as a validator.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        $spec = array(
            'name' => $this->getName(),
            'required' => true
        );

        return $spec;
    }
}
