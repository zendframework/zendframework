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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View\Helper;

/**
 * @uses       \Zend\Json\Json
 * @uses       \Zend\View\Helper\AbstractHelper
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class HtmlElement extends AbstractHelper
{
    /**
     * EOL character
     */
    const EOL = "\n";

    /**
     * The tag closing bracket
     *
     * @var string
     */
    protected $_closingBracket = null;

    /**
     * Get the tag closing bracket
     *
     * @return string
     */
    public function getClosingBracket()
    {
        if (!$this->_closingBracket) {
            if ($this->_isXhtml()) {
                $this->_closingBracket = ' />';
            } else {
                $this->_closingBracket = '>';
            }
        }

        return $this->_closingBracket;
    }

    /**
     * Is doctype XHTML?
     *
     * @return boolean
     */
    protected function _isXhtml()
    {
        $doctype = $this->view->plugin('doctype');
        return $doctype->isXhtml();
    }

    /**
     * Converts an associative array to a string of tag attributes.
     *
     * @access public
     *
     * @param array $attribs From this array, each key-value pair is
     * converted to an attribute name and value.
     *
     * @return string The XHTML for the attributes.
     */
    protected function _htmlAttribs($attribs)
    {
        $xhtml   = '';
        $escaper = $this->view->plugin('escape');
        foreach ((array) $attribs as $key => $val) {
            $key = $escaper($key);

            if (('on' == substr($key, 0, 2)) || ('constraints' == $key)) {
                // Don't escape event attributes; _do_ substitute double quotes with singles
                if (!is_scalar($val)) {
                    // non-scalar data should be cast to JSON first
                    $val = \Zend\Json\Json::encode($val);
                }
                // Escape single quotes inside event attribute values.
                // This will create html, where the attribute value has
                // single quotes around it, and escaped single quotes or
                // non-escaped double quotes inside of it
                $val = str_replace('\'', '&#39;', $val);
            } else {
                if (is_array($val)) {
                    $val = implode(' ', $val);
                }
                $val = $escaper($val);
            }

            if ('id' == $key) {
                $val = $this->_normalizeId($val);
            }

            if (strpos($val, '"') !== false) {
                $xhtml .= " $key='$val'";
            } else {
                $xhtml .= " $key=\"$val\"";
            }

        }
        return $xhtml;
    }

    /**
     * Normalize an ID
     *
     * @param  string $value
     * @return string
     */
    protected function _normalizeId($value)
    {
        if (strstr($value, '[')) {
            if ('[]' == substr($value, -2)) {
                $value = substr($value, 0, strlen($value) - 2);
            }
            $value = trim($value, ']');
            $value = str_replace('][', '-', $value);
            $value = str_replace('[', '-', $value);
        }
        return $value;
    }
}
