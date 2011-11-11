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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Captcha;

use Zend\Service\ReCaptcha\ReCaptcha as ReCaptchaService;

/**
 * ReCaptcha adapter
 *
 * Allows to insert captchas driven by ReCaptcha service
 *
 * @see http://recaptcha.net/apidocs/captcha/
 *
 * @uses       Zend\Captcha\AbstractAdapter
 * @uses       Zend\Service\ReCaptcha\ReCaptcha
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
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
     * @return \Zend\Captcha\ReCaptcha
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
     * @return \Zend\Captcha\ReCaptcha
     */
    public function setPubkey($pubkey)
    {
        $this->getService()->setPublicKey($pubkey);
        return $this;
    }

    /**
     * Constructor
     *
     * @param  array|\Zend\Config\Config $options
     * @return void
     */
    public function __construct($options = null)
    {
        $this->setService(new ReCaptchaService());
        $this->_serviceParams = $this->getService()->getParams();
        $this->_serviceOptions = $this->getService()->getOptions();

        parent::__construct($options);

        if ($options instanceof \Zend\Config\Config) {
            $options = $options->toArray();
        }
        if (!empty($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Set service object
     *
     * @param  Zend\Service\ReCaptcha\ReCaptcha $service
     * @return Zend\Captcha\ReCaptcha
     */
    public function setService(ReCaptchaService $service)
    {
        $this->_service = $service;
        return $this;
    }

    /**
     * Retrieve ReCaptcha service object
     *
     * @return Zend\Service\ReCaptcha\ReCaptcha
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
     * @return \Zend\Captcha\ReCaptcha
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
     * @see Zend_Form_Captcha_Adapter::generate()
     * @return string
     */
    public function generate()
    {
        return "";
    }

    /**
     * Validate captcha
     *
     * @see    Zend\Validator::isValid()
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        if (!is_array($value) && !is_array($context)) {
            $this->_error(self::MISSING_VALUE);
            return false;
        }

        if (!is_array($value) && is_array($context)) {
            $value = $context;
        }

        if (empty($value[$this->_CHALLENGE]) || empty($value[$this->_RESPONSE])) {
            $this->_error(self::MISSING_VALUE);
            return false;
        }

        $service = $this->getService();

        $res = $service->verify($value[$this->_CHALLENGE], $value[$this->_RESPONSE]);

        if (!$res) {
            $this->_error(self::ERR_CAPTCHA);
            return false;
        }

        if (!$res->isValid()) {
            $this->_error(self::BAD_CAPTCHA, $res->getErrorCode());
            $service->setParam('error', $res->getErrorCode());
            return false;
        }

        return true;
    }

    /**
     * Render captcha
     *
     * @param  Zend\View\Renderer $view
     * @param  mixed $element
     * @return string
     */
    public function render(\Zend\View\Renderer $view = null, $element = null)
    {
        $name = null;
        if ($element instanceof \Zend\Form\Element) {
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
