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
 * @package    Zend_Captcha
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Captcha;

use Traversable,
    Zend\Form\Element,
    Zend\Service\ReCaptcha\ReCaptcha as ReCaptchaService,
    Zend\View\Renderer\RendererInterface as Renderer;

/**
 * ReCaptcha adapter
 *
 * Allows to insert captchas driven by ReCaptcha service
 *
 * @see http://recaptcha.net/apidocs/captcha/
 *
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ReCaptcha extends AbstractAdapter
{
    /**@+
     * ReCaptcha Field names
     * @var string
     */
    protected $_CHALLENGE = 'recaptcha_challenge_field';
    protected $_RESPONSE  = 'recaptcha_response_field';
    /**@-*/

    /**
     * Recaptcha service object
     *
     * @var Zend_Service_Recaptcha
     */
    protected $_service;

    /**
     * Parameters defined by the service
     *
     * @var array
     */
    protected $_serviceParams = array();

    /**
     * Options defined by the service
     *
     * @var array
     */
    protected $_serviceOptions = array();

    /**#@+
     * Error codes
     */
    const MISSING_VALUE = 'missingValue';
    const ERR_CAPTCHA   = 'errCaptcha';
    const BAD_CAPTCHA   = 'badCaptcha';
    /**#@-*/

    /**
     * Error messages
     * @var array
     */
    protected $_messageTemplates = array(
        self::MISSING_VALUE => 'Missing captcha fields',
        self::ERR_CAPTCHA   => 'Failed to validate captcha',
        self::BAD_CAPTCHA   => 'Captcha value is wrong: %value%',
    );

    /**
     * Retrieve ReCaptcha Private key
     *
     * @return string
     */
    public function getPrivkey()
    {
        return $this->getService()->getPrivateKey();
    }

    /**
     * Retrieve ReCaptcha Public key
     *
     * @return string
     */
    public function getPubkey()
    {
        return $this->getService()->getPublicKey();
    }

    /**
     * Set ReCaptcha Private key
     *
     * @param string $privkey
     * @return ReCaptcha
     */
    public function setPrivkey($privkey)
    {
        $this->getService()->setPrivateKey($privkey);
        return $this;
    }

    /**
     * Set ReCaptcha public key
     *
     * @param string $pubkey
     * @return ReCaptcha
     */
    public function setPubkey($pubkey)
    {
        $this->getService()->setPublicKey($pubkey);
        return $this;
    }

    /**
     * Constructor
     *
     * @param  array|Config $options
     * @return void
     */
    public function __construct($options = null)
    {
        $this->setService(new ReCaptchaService());
        $this->_serviceParams = $this->getService()->getParams();
        $this->_serviceOptions = $this->getService()->getOptions();

        parent::__construct($options);

        if (!empty($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Set service object
     *
     * @param  ReCaptchaService $service
     * @return ReCaptcha
     */
    public function setService(ReCaptchaService $service)
    {
        $this->_service = $service;
        return $this;
    }

    /**
     * Retrieve ReCaptcha service object
     *
     * @return ReCaptchaService
     */
    public function getService()
    {
        return $this->_service;
    }

    /**
     * Set option
     *
     * If option is a service parameter, proxies to the service. The same
     * goes for any service options (distinct from service params)
     *
     * @param  string $key
     * @param  mixed $value
     * @return ReCaptcha
     */
    public function setOption($key, $value)
    {
        $service = $this->getService();
        if (isset($this->_serviceParams[$key])) {
            $service->setParam($key, $value);
            return $this;
        }
        if (isset($this->_serviceOptions[$key])) {
            $service->setOption($key, $value);
            return $this;
        }
        return parent::setOption($key, $value);
    }

    /**
     * Generate captcha
     *
     * @see AbstractAdapter::generate()
     * @return string
     */
    public function generate()
    {
        return "";
    }

    /**
     * Validate captcha
     *
     * @see    \Zend\Validator\Validator::isValid()
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        if (!is_array($value) && !is_array($context)) {
            $this->error(self::MISSING_VALUE);
            return false;
        }

        if (!is_array($value) && is_array($context)) {
            $value = $context;
        }

        if (empty($value[$this->_CHALLENGE]) || empty($value[$this->_RESPONSE])) {
            $this->error(self::MISSING_VALUE);
            return false;
        }

        $service = $this->getService();

        $res = $service->verify($value[$this->_CHALLENGE], $value[$this->_RESPONSE]);

        if (!$res) {
            $this->error(self::ERR_CAPTCHA);
            return false;
        }

        if (!$res->isValid()) {
            $this->error(self::BAD_CAPTCHA, $res->getErrorCode());
            $service->setParam('error', $res->getErrorCode());
            return false;
        }

        return true;
    }

    /**
     * Render captcha
     *
     * @param  Renderer $view
     * @param  mixed $element
     * @return string
     */
    public function render(Renderer $view = null, $element = null)
    {
        $name = null;
        if ($element instanceof Element) {
            $name = $element->getBelongsTo();
        }

        return $this->getService()->getHTML($name);
    }

    /**
     * Get captcha decorator
     *
     * @return string
     */
    public function getDecorator()
    {
        return "Captcha\ReCaptcha";
    }
}
