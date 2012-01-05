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

/**
 * @namespace
 */
namespace Zend\Form\Decorator;

use Zend\Form\Decorator,
    Zend\Config\Config,
    Zend\Form;

/**
 * Zend_Form_Decorator_Abstract
 *
 * @uses       \Zend\Form\Decorator\Exception
 * @uses       \Zend\Form\Decorator
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractDecorator implements Decorator
{
    /**
     * Placement constants
     */
    const APPEND  = 'APPEND';
    const PREPEND = 'PREPEND';

    /**
     * Default placement: append
     * @var string
     */
    protected $_placement = 'APPEND';

    /**
     * @var \Zend\Form\Element|\Zend\Form\Form
     */
    protected $_element;

    /**
     * Decorator options
     * @var array
     */
    protected $_options = array();

    /**
     * Separator between new content and old
     * @var string
     */
    protected $_separator = PHP_EOL;

    /**
     * Constructor
     *
     * @param  array|\Zend\Config\Config $options
     * @return void
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Config) {
            $this->setConfig($options);
        }
    }

    /**
     * Set options
     *
     * @param  array $options
     * @return \Zend\Form\Decorator\AbstractDecorator
     */
    public function setOptions(array $options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Set options from config object
     *
     * @param  \Zend\Config\Config $config
     * @return \Zend\Form\Decorator\AbstractDecorator
     */
    public function setConfig(Config $config)
    {
        return $this->setOptions($config->toArray());
    }

    /**
     * Set option
     *
     * @param  string $key
     * @param  mixed $value
     * @return \Zend\Form\Decorator\AbstractDecorator
     */
    public function setOption($key, $value)
    {
        $this->_options[(string) $key] = $value;
        return $this;
    }

    /**
     * Get option
     *
     * @param  string $key
     * @return mixed
     */
    public function getOption($key)
    {
        $key = (string) $key;
        if (isset($this->_options[$key])) {
            return $this->_options[$key];
        }

        return null;
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Remove single option
     *
     * @param mixed $key
     * @return void
     */
    public function removeOption($key)
    {
        if (null !== $this->getOption($key)) {
            unset($this->_options[$key]);
            return true;
        }

        return false;
    }

    /**
     * Clear all options
     *
     * @return \Zend\Form\Decorator\AbstractDecorator
     */
    public function clearOptions()
    {
        $this->_options = array();
        return $this;
    }

    /**
     * Set current form element
     *
     * @param  \Zend\Form\Form_Element|\Zend\Form\Form $element
     * @return \Zend\Form\Decorator\AbstractDecorator
     * @throws \Zend\Form\Decorator\Exception on invalid element type
     */
    public function setElement($element)
    {
        if ((!$element instanceof Form\Element)
            && (!$element instanceof Form\Form)
            && (!$element instanceof Form\DisplayGroup))
        {
            throw new Exception\InvalidArgumentException('Invalid element type passed to decorator');
        }

        $this->_element = $element;
        return $this;
    }

    /**
     * Retrieve current element
     *
     * @return \Zend\Form\Form_Element|\Zend\Form\Form
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Determine if decorator should append or prepend content
     *
     * @return string
     */
    public function getPlacement()
    {
        $placement = $this->_placement;
        if (null !== ($placementOpt = $this->getOption('placement'))) {
            $placementOpt = strtoupper($placementOpt);
            switch ($placementOpt) {
                case self::APPEND:
                case self::PREPEND:
                    $placement = $this->_placement = $placementOpt;
                    break;
                case false:
                    $placement = $this->_placement = null;
                    break;
                default:
                    break;
            }
            $this->removeOption('placement');
        }

        return $placement;
    }

    /**
     * Retrieve separator to use between old and new content
     *
     * @return string
     */
    public function getSeparator()
    {
        $separator = $this->_separator;
        if (null !== ($separatorOpt = $this->getOption('separator'))) {
            $separator = $this->_separator = (string) $separatorOpt;
            $this->removeOption('separator');
        }
        return $separator;
    }

    /**
     * Decorate content and/or element
     *
     * @param  string $content
     * @return string
     * @throws \Zend\Form\Decorator\Exception when unimplemented
     */
    public function render($content)
    {
        throw new Exception\RunTimeException('render() not implemented');
    }
}
