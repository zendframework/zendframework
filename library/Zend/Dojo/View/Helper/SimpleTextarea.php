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

/**
 * dijit.form.SimpleTextarea view helper
 *
 * @package    Zend_Dojo
 * @subpackage View
 */
class SimpleTextarea extends Dijit
{
    /**
     * @var string Dijit type
     */
    protected $_dijit  = 'dijit.form.SimpleTextarea';

    /**
     * @var string HTML element type
     */
    protected $_elementType = 'textarea';

    /**
     * @var string Dojo module
     */
    protected $_module = 'dijit.form.SimpleTextarea';

    /**
     * dijit.form.SimpleTextarea
     *
     * @param  string $id
     * @param  string $value
     * @param  array $params  Parameters to use for dijit creation
     * @param  array $attribs HTML attributes
     * @return string
     */
    public function __invoke($id = null, $value = null, array $params = array(), array $attribs = array())
    {
        if (!array_key_exists('id', $attribs)) {
            $attribs['id']    = $id;
        }
        $attribs['name']  = $id;

        $attribs = $this->_prepareDijit($attribs, $params, 'textarea');

        $html = '<textarea' . $this->_htmlAttribs($attribs) . '>'
              . $this->view->escape($value)
              . "</textarea>\n";

        return $html;
    }
}
