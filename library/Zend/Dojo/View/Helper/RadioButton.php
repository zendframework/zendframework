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

use Zend\I18n\Filter\Alnum as AlnumFilter;

/**
 * Dojo RadioButton dijit
 *
 * @package    Zend_Dojo
 * @subpackage View
  */
class RadioButton extends Dijit
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dijit.form.RadioButton';

    /**
     * Dojo module to use
     * @var string
     */
    protected $_module = 'dijit.form.CheckBox';

    /**
     * dijit.form.RadioButton
     *
     * @param  string $id
     * @param  string $value
     * @param  array $params  Parameters to use for dijit creation
     * @param  array $attribs HTML attributes
     * @param  array $options Array of radio options
     * @param  string $listsep String with which to separate options
     * @return string
     */
    public function __invoke(
        $id = null,
        $value = null,
        array $params = array(),
        array $attribs = array(),
        array $options = null,
        $listsep = "<br />\n"
    ) {
        $attribs['name'] = $id;
        if (!array_key_exists('id', $attribs)) {
            $attribs['id'] = $id;
        }
        $attribs = $this->_prepareDijit($attribs, $params, 'element');

        if (is_array($options) && $this->_useProgrammatic() && !$this->_useProgrammaticNoScript()) {
            $baseId = $id;
            if (array_key_exists('id', $attribs)) {
                $baseId = $attribs['id'];
            }
            $filter = new AlnumFilter();
            foreach (array_keys($options) as $key) {
                $optId = $baseId . '-' . $filter->filter($key);
                $this->_createDijit($this->_dijit, $optId, array());
            }
        }

        return $this->view->formRadio($id, $value, $attribs, $options, $listsep);
    }
}
