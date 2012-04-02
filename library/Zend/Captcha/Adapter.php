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

/**
 * Generic Captcha adapter interface
 *
 * Each specific captcha implementation should implement this interface
 *
 * @uses       Zend\Validator\Validator
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Adapter extends \Zend\Validator\Validator
{
    /**
     * Generate a new captcha
     *
     * @return string new captcha ID
     */
    public function generate();

    /**
     * Display the captcha
     *
     * @param  \Zend\View\Renderer $view
     * @param  mixed $element
     * @return string
     */
    public function render(\Zend\View\Renderer $view = null, $element = null);

    /**
     * Set captcha name
     *
     * @param  string $name
     * @return \Zend\Captcha\Adapter
     */
    public function setName($name);

    /**
     * Get captcha name
     *
     * @return string
     */
    public function getName();

    /**
     * Get optional private decorator for this captcha type
     *
     * @return \Zend\Form\Decorator|string
     */
    public function getDecorator();
}
