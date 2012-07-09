<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace Zend\Dojo\Form\Element;

/**
 * TextBox dijit
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage Form_Element
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
