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
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\Exception;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormElementErrors extends AbstractHelper
{
    /**@+
     * @var string Templates for the open/close/separators for message tags
     */
    protected $messageCloseString     = '</li></ul>';
    protected $messageOpenFormat      = '<ul%s><li>';
    protected $messageSeparatorString = '</li><li>';
    /**@-*/

    /**
     * Set the string used to close message representation
     *
     * @param  string $messageCloseString
     * @return FormElementErrors
     */
    public function setMessageCloseString($messageCloseString)
    {
        $this->messageCloseString = (string) $messageCloseString;
        return $this;
    }
    
    /**
     * Get the string used to close message representation
     *
     * @return string
     */
    public function getMessageCloseString()
    {
        return $this->messageCloseString;
    }

    /**
     * Set the formatted string used to open message representation
     *
     * @param  string $messageOpenFormat
     * @return FormElementErrors
     */
    public function setMessageOpenFormat($messageOpenFormat)
    {
        $this->messageOpenFormat = (string) $messageOpenFormat;
        return $this;
    }
    
    /**
     * Get the formatted string used to open message representation
     *
     * @return string
     */
    public function getMessageOpenFormat()
    {
        return $this->messageOpenFormat;
    }

    /**
     * Set the string used to separate messages
     *
     * @param  string $messageSeparatorString
     * @return FormElementErrors
     */
    public function setMessageSeparatorString($messageSeparatorString)
    {
        $this->messageSeparatorString = (string) $messageSeparatorString;
        return $this;
    }
    
    /**
     * Get the string used to separate messages
     *
     * @return string
     */
    public function getMessageSeparatorString()
    {
        return $this->messageSeparatorString;
    }

    /**
     * Render validation errors for the provided $element
     * 
     * @param  ElementInterface $element 
     * @return string
     */
    public function render(ElementInterface $element, array $attributes = array())
    {
        $messages = $element->getMessages();
        if (empty($messages)) {
            return '';
        }
        if (!is_array($messages) && !$messages instanceof Traversable) {
            throw new Exception\DomainException(sprintf(
                '%s expects that $element->getMessages() will return an array or Traversable; received "%s"',
                __METHOD__,
                (is_object($messages) ? get_class($messages) : gettype($messages))
            ));
        }

        // Prepare attributes for opening tag
        $attributes = $this->createAttributesString($attributes);
        if (!empty($attributes)) {
            $attributes = ' ' . $attributes;
        }

        // Flatten message array
        $escape          = $this->getEscapeHelper();
        $messagesToPrint = array();
        array_walk_recursive($messages, function($item) use (&$messagesToPrint, $escape) {
            $messagesToPrint[] = $escape($item);
        });

        // Generate markup
        $markup  = sprintf($this->getMessageOpenFormat(), $attributes);
        $markup .= implode($this->getMessageSeparatorString(), $messagesToPrint);
        $markup .= $this->getMessageCloseString();

        return $markup;
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     * 
     * @param  ElementInterface $element 
     * @return string
     */
    public function __invoke(ElementInterface $element)
    {
        return $this->render($element);
    }
}
