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

namespace Zend\Form\View\Helper;

use Zend\Loader\Pluggable;
use Zend\View\Helper\AbstractHelper as BaseAbstractHelper;
use Zend\View\Helper\Doctype;
use Zend\View\Helper\Escape;

/**
 * Base functionality for all form view helpers
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractHelper extends BaseAbstractHelper
{
    /**
     * @var Doctype
     */
    protected $doctypeHelper;

    /**
     * @var Escape
     */
    protected $escapeHelper;

    /**
     * Set value for doctype
     *
     * @param  string $doctype
     * @return AbstractHelper
     */
    public function setDoctype($doctype)
    {
        $this->getDoctypeHelper()->setDoctype($doctype);
        return $this;
    }
    
    /**
     * Get value for doctype
     *
     * @return string
     */
    public function getDoctype()
    {
        return $this->getDoctypeHelper()->getDoctype();
    }

    /**
     * Set value for character encoding
     *
     * @param  string encoding
     * @return AbstractHelper
     */
    public function setEncoding($encoding)
    {
        $this->getEscapeHelper()->setEncoding($encoding);
        return $this;
    }
    
    /**
     * Get character encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->getEscapeHelper()->getEncoding();
    }

    /**
     * Create a string of all attribute/value pairs
     *
     * Escapes all attribute values
     * 
     * @param  array $attributes 
     * @return string
     */
    public function createAttributesString(array $attributes)
    {
        $escape  = $this->getEscapeHelper();
        $strings = array();
        foreach ($attributes as $key => $value) {
            $strings[] = sprintf('%s="%s"', $key, $escape($value));
        }
        return implode(' ', $strings);
    }

    /**
     * Retrieve the doctype helper
     * 
     * @return Doctype
     */
    protected function getDoctypeHelper()
    {
        if ($this->doctypeHelper) {
            return $this->doctypeHelper;
        }

        if ($this->view instanceof Pluggable) {
            $this->doctypeHelper = $this->view->plugin('doctype');
        }

        if (!$this->doctypeHelper instanceof Doctype) {
            $this->doctypeHelper = new Doctype();
        }

        return $this->doctypeHelper;
    }

    /**
     * Retrieve the escape helper
     * 
     * @return Escape
     */
    protected function getEscapeHelper()
    {
        if ($this->escapeHelper) {
            return $this->escapeHelper;
        }

        if ($this->view instanceof Pluggable) {
            $this->escapeHelper = $this->view->plugin('escape');
        }

        if (!$this->escapeHelper instanceof Escape) {
            $this->escapeHelper = new Escape();
        }

        return $this->escapeHelper;
    }
}
