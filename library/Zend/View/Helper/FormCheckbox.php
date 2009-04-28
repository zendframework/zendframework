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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';


/**
 * Helper to generate a "checkbox" element
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Helper_FormCheckbox extends Zend_View_Helper_FormElement
{
    /**
     * Default checked/unchecked options
     * @var array
     */
    protected static $_defaultCheckedOptions = array(
        'checked'   => '1',
        'unChecked' => '0'
    );

    /**
     * Generates a 'checkbox' element.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     * @param mixed $value The element value.
     * @param array $attribs Attributes for the element tag.
     * @return string The element XHTML.
     */
    public function formCheckbox($name, $value = null, $attribs = null, array $checkedOptions = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, id, value, attribs, options, listsep, disable

        $checked = false;
        if (isset($attribs['checked']) && $attribs['checked']) {
            $checked = true;
            unset($attribs['checked']);
        } elseif (isset($attribs['checked'])) {
            $checked = false;
            unset($attribs['checked']);
        }

        $checkedOptions = self::determineCheckboxInfo($value, $checked, $checkedOptions);

        // is the element disabled?
        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }

        // XHTML or HTML end tag?
        $endTag = ' />';
        if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
            $endTag= '>';
        }

        // build the element
        $xhtml = '';
        if (!strstr($name, '[]')) {
            $xhtml = $this->_hidden($name, $checkedOptions['unCheckedValue']);
        }
        $xhtml .= '<input type="checkbox"'
                . ' name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . ' value="' . $this->view->escape($checkedOptions['checkedValue']) . '"'
                . $checkedOptions['checkedString']
                . $disabled
                . $this->_htmlAttribs($attribs)
                . $endTag;

        return $xhtml;
    }

    /**
     * Determine checkbox information
     * 
     * @param  string $value 
     * @param  bool $checked 
     * @param  array|null $checkedOptions 
     * @return array
     */
    public static function determineCheckboxInfo($value, $checked, array $checkedOptions = null)
    {
        // Checked/unchecked values
        $checkedValue   = null;
        $unCheckedValue = null;
        if (is_array($checkedOptions)) {
            if (array_key_exists('checked', $checkedOptions)) {
                $checkedValue = (string) $checkedOptions['checked'];
                unset($checkedOptions['checked']);
            }
            if (array_key_exists('unChecked', $checkedOptions)) {
                $unCheckedValue = (string) $checkedOptions['unChecked'];
                unset($checkedOptions['unChecked']);
            }
            if (null === $checkedValue) {
                $checkedValue = array_shift($checkedOptions);
            }
            if (null === $unCheckedValue) {
                $unCheckedValue = array_shift($checkedOptions);
            }
        } elseif ($value !== null) {
            $unCheckedValue = self::$_defaultCheckedOptions['unChecked'];
        } else {
            $checkedValue   = self::$_defaultCheckedOptions['checked'];
            $unCheckedValue = self::$_defaultCheckedOptions['unChecked'];
        }

        // is the element checked?
        $checkedString = '';
        if ($checked || ($value === $checkedValue)) {
            $checkedString = ' checked="checked"';
            $checked = true;
        } else {
            $checked = false;
        }

        // Checked value should be value if no checked options provided
        if ($checkedValue == null) {
            $checkedValue = $value;
        }

        return array(
            'checked'        => $checked,
            'checkedString'  => $checkedString,
            'checkedValue'   => $checkedValue,
            'unCheckedValue' => $unCheckedValue,
        );
    }
}
