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
namespace Zend\Dojo\Form\Decorator;

use Zend\Form\Decorator\ViewHelper as ViewHelperDecorator,
    Zend\Form\Decorator\Exception\RunTimeException as DecoratorException;

/**
 * Zend_Dojo_Form_Decorator_DijitElement
 *
 * Render a dojo dijit element via a view helper
 *
 * Accepts the following options:
 * - separator: string with which to separate passed in content and generated content
 * - placement: whether to append or prepend the generated content to the passed in content
 * - helper:    the name of the view helper to use
 *
 * Assumes the view helper accepts three parameters, the name, value, and
 * optional attributes; these will be provided by the element.
 *
 * @uses       \Zend\Form\Decorator\Exception
 * @uses       \Zend\Form\Decorator\ViewHelper
 * @package    Zend_Dojo
 * @subpackage Form_Decorator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DijitElement extends ViewHelperDecorator
{
    /**
     * Element attributes
     * @var array
     */
    protected $_attribs;

    /**
     * Element types that represent buttons
     * @var array
     */
    protected $_buttonTypes = array(
        'Zend\Dojo\Form\Element\Button',
        'Zend\Form\Element\Button',
        'Zend\Form\Element\Reset',
        'Zend\Form\Element\Submit',
    );

    /**
     * Dijit option parameters
     * @var array
     */
    protected $_dijitParams = array();

    /**
     * Get element attributes
     *
     * @return array
     */
    public function getElementAttribs()
    {
        if (null === $this->_attribs) {
            $this->_attribs = parent::getElementAttribs();
            if (array_key_exists('dijitParams', $this->_attribs)) {
                $this->setDijitParams($this->_attribs['dijitParams']);
                unset($this->_attribs['dijitParams']);
            }
        }

        return $this->_attribs;
    }

    /**
     * Set a single dijit option parameter
     *
     * @param  string $key
     * @param  mixed $value
     * @return \Zend\Dojo\Form\Decorator\DijitContainer
     */
    public function setDijitParam($key, $value)
    {
        $this->_dijitParams[(string) $key] = $value;
        return $this;
    }

    /**
     * Set dijit option parameters
     *
     * @param  array $params
     * @return \Zend\Dojo\Form\Decorator\DijitContainer
     */
    public function setDijitParams(array $params)
    {
        $this->_dijitParams = array_merge($this->_dijitParams, $params);
        return $this;
    }

    /**
     * Retrieve a single dijit option parameter
     *
     * @param  string $key
     * @return mixed|null
     */
    public function getDijitParam($key)
    {
        $this->getElementAttribs();
        $key = (string) $key;
        if (array_key_exists($key, $this->_dijitParams)) {
            return $this->_dijitParams[$key];
        }

        return null;
    }

    /**
     * Get dijit option parameters
     *
     * @return array
     */
    public function getDijitParams()
    {
        $this->getElementAttribs();
        return $this->_dijitParams;
    }

    /**
     * Render an element using a view helper
     *
     * Determine view helper from 'helper' option, or, if none set, from
     * the element type. Then call as
     * helper($element->getName(), $element->getValue(), $element->getAttribs())
     *
     * @param  string $content
     * @return string
     * @throws \Zend\Form\Decorator\Exception if element or view are not registered
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view = $element->getView();
        if (null === $view) {
            throw new DecoratorException('DijitElement decorator cannot render without a registered view object');
        }

        $options = null;
        $helper    = $this->getHelper();
        $separator = $this->getSeparator();
        $value     = $this->getValue($element);
        $attribs   = $this->getElementAttribs();
        $name      = $element->getFullyQualifiedName();

        $dijitParams = $this->getDijitParams();
        $dijitParams['required'] = $element->isRequired();

        $id = $element->getId();
        if ($view->plugin('dojo')->hasDijit($id)) {
            trigger_error(sprintf('Duplicate dijit ID detected for id "%s; temporarily generating uniqid"', $id), E_USER_NOTICE);
            $base = $id;
            do {
                $id = $base . '-' . uniqid();
            } while ($view->plugin('dojo')->hasDijit($id));
        }
        $attribs['id'] = $id;

        if (array_key_exists('options', $attribs)) {
               $options = $attribs['options'];
        }

        $helper = $view->plugin($helper);
        $elementContent = $helper($name, $value, $dijitParams, $attribs, $options);
        switch ($this->getPlacement()) {
            case self::APPEND:
                return $content . $separator . $elementContent;
            case self::PREPEND:
                return $elementContent . $separator . $content;
            default:
                return $elementContent;
        }
    }
}
