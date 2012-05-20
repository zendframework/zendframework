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

namespace Zend\Form\View\Helper\Captcha;

use Traversable;
use Zend\Captcha\AdapterInterface as CaptchaAdapter;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\View\Helper\FormInput;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractWord extends FormInput
{
    const CAPTCHA_APPEND  = 'append';
    const CAPTCHA_PREPEND = 'prepend';

    protected $inputHelper;
    protected $captchaPosition = self::CAPTCHA_APPEND;
    protected $separator = '';

    /**
     * Set value for captchaPosition
     *
     * @param  mixed captchaPosition
     * @return $this
     */
    public function setCaptchaPosition($captchaPosition)
    {
        $captchaPosition = strtolower($captchaPosition);
        if (!in_array($captchaPosition, array(self::CAPTCHA_APPEND, self::CAPTCHA_PREPEND))) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects either %s::CAPTCHA_APPEND or %s::CAPTCHA_PREPEND; received "%s"',
                __METHOD__,
                __CLASS__,
                __CLASS__,
                (string) $captchaPosition
            ));
        }
        $this->captchaPosition = $captchaPosition;
        return $this;
    }
    
    /**
     * Get position of captcha
     *
     * @return string
     */
    public function getCaptchaPosition()
    {
        return $this->captchaPosition;
    }

    /**
     * Set separator string for captcha and inputs
     *
     * @param  string $separator
     * @return Word
     */
    public function setSeparator($separator)
    {
        $this->separator = (string) $separator;
        return $this;
    }
    
    /**
     * Get separator for captcha and inputs
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * Render captcha form elements for the given element
     *
     * Creates and returns:
     * - Hidden input with captcha identifier (name[id])
     * - Text input for entering captcha value (name[input])
     *
     * More specific renderers will consume this and render it.
     * 
     * @param  ElementInterface $element 
     * @return string
     */
    protected function renderCaptchaInputs(ElementInterface $element)
    {
        $name = $element->getName();
        if (empty($name)) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes = $element->getAttributes();

        if (!isset($attributes['captcha']) 
            || !$attributes['captcha'] instanceof CaptchaAdapter
        ) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has a "captcha" attribute implementing Zend\Captcha\AdapterInterface; none found',
                __METHOD__
            ));
        }

        $captcha = $attributes['captcha'];
        unset($attributes['captcha']);

        $hidden    = $this->renderCaptchaHidden($captcha, $attributes);
        $input     = $this->renderCaptchaInput($captcha, $attributes);
        $separator = $this->getSeparator();

        return $hidden . $separator . $input;
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

    /**
     * Render the hidden input with the captcha identifier
     * 
     * @param  CaptchaAdapter $captcha 
     * @param  array $attributes 
     * @return string
     */
    protected function renderCaptchaHidden(CaptchaAdapter $captcha, array $attributes)
    {
        $attributes['type']  = 'hidden';
        $attributes['name'] .= '[id]';
        if (method_exists($captcha, 'getId')) {
            $attributes['value'] = $captcha->getId();
        } elseif (array_key_exists('value', $attributes)) {
            if (is_array($attributes['value']) && array_key_exists('id', $attributes['value'])) {
                $attributes['value'] = $attributes['value']['id'];
            }
        }
        $closingBracket      = $this->getInlineClosingBracket();
        $hidden              = sprintf(
            '<input %s%s', 
            $this->createAttributesString($attributes), 
            $closingBracket
        );
        return $hidden;
    }

    /**
     * Render the input for capturing the captcha value from the client
     * 
     * @param  CaptchaAdapter $captcha 
     * @param  array $attributes 
     * @return string
     */
    protected function renderCaptchaInput(CaptchaAdapter $captcha, array $attributes)
    {
        $attributes['type']  = 'text';
        $attributes['name'] .= '[input]';
        if (array_key_exists('value', $attributes)) {
            unset($attributes['value']);
        }
        $closingBracket      = $this->getInlineClosingBracket();
        $input               = sprintf(
            '<input %s%s', 
            $this->createAttributesString($attributes), 
            $closingBracket
        );
        return $input;
    }
}
