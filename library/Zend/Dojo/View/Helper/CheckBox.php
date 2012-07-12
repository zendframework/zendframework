<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace Zend\Dojo\View\Helper;

use Zend\View\Helper\FormCheckbox as FormCheckboxHelper;

/**
 * Dojo CheckBox dijit
 *
 * @package    Zend_Dojo
 * @subpackage View
  */
class CheckBox extends Dijit
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dijit.form.CheckBox';

    /**
     * Element type
     * @var string
     */
    protected $_elementType = 'checkbox';

    /**
     * Dojo module to use
     * @var string
     */
    protected $_module = 'dijit.form.CheckBox';

    /**
     * dijit.form.CheckBox
     *
     * @param  int $id
     * @param  string $content
     * @param  array $params  Parameters to use for dijit creation
     * @param  array $attribs HTML attributes
     * @param  array $checkedOptions Should contain either two items, or the keys checkedValue and uncheckedValue
     * @return string
     */
    public function __invoke($id = null, $value = null, array $params = array(), array $attribs = array(), array $checkedOptions = null)
    {
        // Prepare the checkbox options
        $checked = false;
        if (isset($attribs['checked']) && $attribs['checked']) {
            $checked = true;
        } elseif (isset($attribs['checked'])) {
            $checked = false;
        }
        $checkboxInfo = FormCheckboxHelper::determineCheckboxInfo($value, $checked, $checkedOptions);
        $attribs['checked'] = $checkboxInfo['checked'];
        if (!array_key_exists('id', $attribs)) {
            $attribs['id'] = $id;
        }

        $attribs = $this->_prepareDijit($attribs, $params, 'element');

        // strip options so they don't show up in markup
        if (array_key_exists('options', $attribs)) {
            unset($attribs['options']);
        }

        // and now we create it:
        $html = '';
        if (!strstr($id, '[]')) {
            // hidden element for unchecked value
            $html .= $this->_renderHiddenElement($id, $checkboxInfo['uncheckedValue']);
        }

        // and final element
        $html .= $this->_createFormElement($id, $checkboxInfo['checkedValue'], $params, $attribs);

        return $html;
    }
}
