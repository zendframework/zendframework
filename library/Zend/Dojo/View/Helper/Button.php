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
 * Dojo Button dijit
 *
 * @package    Zend_Dojo
 * @subpackage View
 */
class Button extends Dijit
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dijit.form.Button';

    /**
     * Dojo module to use
     * @var string
     */
    protected $_module = 'dijit.form.Button';

    /**
     * dijit.form.Button
     *
     * @param  string $id
     * @param  string $value
     * @param  array $params  Parameters to use for dijit creation
     * @param  array $attribs HTML attributes
     * @return string
     */
    public function __invoke($id = null, $value = null, array $params = array(), array $attribs = array())
    {
        $attribs['name'] = $id;
        if (!array_key_exists('id', $attribs)) {
            $attribs['id'] = $id;
        }
        $attribs = $this->_prepareDijit($attribs, $params, 'element');

        return $this->view->formButton($id, $value, $attribs);
    }
}
