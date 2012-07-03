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
use Zend\Captcha as ZendCaptcha;
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
class Captcha extends Element implements InputProviderInterface
{
    /**
     * @var \Zend\Captcha\AdapterInterface
     */
    protected $captcha;

    /**
     * Accepted options for Captcha:
     * - captcha: a valid Zend\Captcha\AdapterInterface
     *
     * @param array|\Traversable $options
     * @return Captcha
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['captcha'])) {
            $this->setCaptcha($options['captcha']);
        }

        return $this;
    }

    /**
     * Set captcha
     *
     * @param  array|ZendCaptcha\AdapterInterface $captcha
     * @return Captcha
     */
    public function setCaptcha($captcha)
    {
        if (is_array($captcha) || $captcha instanceof Traversable) {
            $captcha = ZendCaptcha\Factory::factory($captcha);
        } elseif (!$captcha instanceof ZendCaptcha\AdapterInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects either a Zend\Captcha\AdapterInterface or specification to pass to Zend\Captcha\Factory; received "%s"',
                __METHOD__,
                (is_object($captcha) ? get_class($captcha) : gettype($captcha))
            ));
        }
        $this->captcha = $captcha;

        return $this;
    }

    /**
     * Retrieve captcha (if any)
     *
     * @return null|ZendCaptcha\AdapterInterface
     */
    public function getCaptcha()
    {
        return $this->captcha;
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
            'required' => true,
            'filters' => array(
                array('name' => 'Zend\Filter\StringTrim'),
            ),
        );

        // Test that we have a captcha before adding it to the spec
        $captcha = $this->getCaptcha();
        if ($captcha instanceof ZendCaptcha\AdapterInterface) {
            $spec['validators'] = array($captcha);
        }

        return $spec;
    }
}
