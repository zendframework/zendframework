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

/**
 * @namespace
 */
namespace Zend\Form\Element;

use Zend\Validator\AbstractValidator;

/**
 * Password form element
 *
 * @uses       \Zend\Form\Element\Xhtml
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Password extends Xhtml
{
    /**
     * Use formPassword view helper by default
     * @var string
     */
    public $helper = 'formPassword';

    /**
     * Whether or not to render the password
     * @var bool
     */
    public $renderPassword = false;

    /**
     * Set flag indicating whether or not to render the password
     * @param  bool $flag
     * @return \Zend\Form\Element\Password
     */
    public function setRenderPassword($flag)
    {
        $this->renderPassword = (bool) $flag;
        return $this;
    }

    /**
     * Get value of renderPassword flag
     *
     * @return bool
     */
    public function renderPassword()
    {
        return $this->renderPassword;
    }

    /**
     * Override isValid()
     *
     * Ensure that validation error messages mask password value.
     *
     * @param  string $value
     * @param  mixed $context
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        foreach ($this->getValidators() as $validator) {
            if ($validator instanceof AbstractValidator) {
                $validator->setValueObscured(true);
            }
        }
        return parent::isValid($value, $context);
    }
}
