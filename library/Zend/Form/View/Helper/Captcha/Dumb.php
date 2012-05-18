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

use Zend\Captcha\Dumb as CaptchaAdapter;
use Zend\Form\ElementInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Dumb extends AbstractWord
{
    /**
     * Render the captcha
     * 
     * @param  ElementInterface $element 
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $attributes = $element->getAttributes();

        if (!isset($attributes['captcha']) 
            || !$attributes['captcha'] instanceof CaptchaAdapter
        ) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has a "captcha" attribute of type Zend\Captcha\Dumb; none found',
                __METHOD__
            ));
        }
        $captcha = $attributes['captcha'];
        $captcha->generate();

        $label = sprintf(
            '%s <b>%s</b>',
            $captcha->getLabel(),
            strrev($captcha->getWord())
        );

        $position     = $this->getCaptchaPosition();
        $separator    = $this->getSeparator();
        $captchaInput = $this->renderCaptchaInputs($element);

        $pattern = '%s%s%s';
        if ($position === self::CAPTCHA_PREPEND) {
            return sprintf($pattern, $captchaInput, $separator, $label);
        }

        return sprintf($pattern, $label, $separator, $captchaInput);
    }
}
