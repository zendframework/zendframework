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
 * @package    Zend_Dojo
 * @subpackage Form_Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Dojo\Form\Element;

/**
 * TextBox dijit
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage Form_Element
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TextBox extends Dijit
{
    /**
     * Use TextBox dijit view helper
     * @var string
     */
    public $helper = 'TextBox';

    /**
     * Set lowercase flag
     *
     * @param  bool $lowercase
     * @return \Zend\Dojo\Form\Element\TextBox
     */
    public function setLowercase($flag)
    {
        $this->setDijitParam('lowercase', (bool) $flag);
        return $this;
    }

    /**
     * Retrieve lowercase flag
     *
     * @return bool
     */
    public function getLowercase()
    {
        if (!$this->hasDijitParam('lowercase')) {
            return false;
        }
        return $this->getDijitParam('lowercase');
    }

    /**
     * Set propercase flag
     *
     * @param  bool $propercase
     * @return \Zend\Dojo\Form\Element\TextBox
     */
    public function setPropercase($flag)
    {
        $this->setDijitParam('propercase', (bool) $flag);
        return $this;
    }

    /**
     * Retrieve propercase flag
     *
     * @return bool
     */
    public function getPropercase()
    {
        if (!$this->hasDijitParam('propercase')) {
            return false;
        }
        return $this->getDijitParam('propercase');
    }

    /**
     * Set uppercase flag
     *
     * @param  bool $uppercase
     * @return \Zend\Dojo\Form\Element\TextBox
     */
    public function setUppercase($flag)
    {
        $this->setDijitParam('uppercase', (bool) $flag);
        return $this;
    }

    /**
     * Retrieve uppercase flag
     *
     * @return bool
     */
    public function getUppercase()
    {
        if (!$this->hasDijitParam('uppercase')) {
            return false;
        }
        return $this->getDijitParam('uppercase');
    }

    /**
     * Set trim flag
     *
     * @param  bool $trim
     * @return \Zend\Dojo\Form\Element\TextBox
     */
    public function setTrim($flag)
    {
        $this->setDijitParam('trim', (bool) $flag);
        return $this;
    }

    /**
     * Retrieve trim flag
     *
     * @return bool
     */
    public function getTrim()
    {
        if (!$this->hasDijitParam('trim')) {
            return false;
        }
        return $this->getDijitParam('trim');
    }

    /**
     * Set maxLength
     *
     * @param  int $length
     * @return \Zend\Dojo\Form\Element\TextBox
     */
    public function setMaxLength($length)
    {
        $this->setDijitParam('maxLength', (int) $length);
        return $this;
    }

    /**
     * Retrieve maxLength
     *
     * @return int|null
     */
    public function getMaxLength()
    {
        return $this->getDijitParam('maxLength');
    }
}
