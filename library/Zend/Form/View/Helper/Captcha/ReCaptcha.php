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
use Zend\Captcha\ReCaptcha as CaptchaAdapter;
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
class ReCaptcha extends FormInput
{
    /**
     * Render ReCaptcha form elements
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $attributes = $element->getAttributes();
        $captcha = $element->getCaptcha();

        if ($captcha === null || !$captcha instanceof CaptchaAdapter) {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has a "captcha" attribute implementing Zend\Captcha\AdapterInterface; none found',
                __METHOD__
            ));
        }

        $name          = $element->getName();
        $id            = isset($attributes['id']) ? $attributes['id'] : $name;
        $challengeName = empty($name) ? 'recaptcha_challenge_field' : $name . '[recaptcha_challenge_field]';
        $responseName  = empty($name) ? 'recaptcha_response_field'  : $name . '[recaptcha_response_field]';
        $challengeId   = $id . '-challenge';
        $responseId    = $id . '-response';

        $markup = $captcha->getService()->getHtml($name);
        $hidden = $this->renderHiddenInput($challengeName, $challengeId, $responseName, $responseId);
        $js     = $this->renderJsEvents($challengeId, $responseId);

        return $hidden . $markup . $js;
    }

    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render hidden input elements for the challenge and response
     *
     * @param  string $challengeName
     * @param  string $challengeId
     * @param  string $responseName
     * @param  string $responseId
     * @return string
     */
    protected function renderHiddenInput($challengeName, $challengeId, $responseName, $responseId)
    {
        $pattern        = '<input type="hidden" %s%s';
        $closingBracket = $this->getInlineClosingBracket();

        $attributes = $this->createAttributesString(array(
            'name' => $challengeName,
            'id'   => $challengeId,
        ));
        $challenge = sprintf($pattern, $attributes, $closingBracket);

        $attributes = $this->createAttributesString(array(
            'name' => $responseName,
            'id'   => $responseId,
        ));
        $response = sprintf($pattern, $attributes, $closingBracket);

        return $challenge . $response;
    }

    /**
     * Create the JS events used to bind the challenge and response values to the submitted form.
     *
     * @param  string $challengeId
     * @param  string $responseId
     * @return string
     */
    protected function renderJsEvents($challengeId, $responseId)
    {
        $js =<<<EOJ
<script type="text/javascript" language="JavaScript">
function windowOnLoad(fn) {
    var old = window.onload;
    window.onload = function() {
        if (old) {
            old();
        }
        fn();
    };
}
function zendBindEvent(el, eventName, eventHandler) {
    if (el.addEventListener){
        el.addEventListener(eventName, eventHandler, false);
    } else if (el.attachEvent){
        el.attachEvent('on'+eventName, eventHandler);
    }
}
windowOnLoad(function(){
    zendBindEvent(
        document.getElementById("$challengeId").form,
        'submit',
        function(e) {
            document.getElementById("$challengeId").value = document.getElementById("recaptcha_challenge_field").value;
            document.getElementById("$responseId").value = document.getElementById("recaptcha_response_field").value;
        }
    );
});
</script>
EOJ;
        return $js;
    }
}
