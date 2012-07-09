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
 * Dojo Button dijit tied to submit input
 *
 * @package    Zend_Dojo
 * @subpackage View
 */
class SubmitButton extends Button
{
    /**
     * @var string Submit input
     */
    protected $_elementType = 'submit';

    /**
     * dijit.form.Button tied to submit input
     *
     * @param  string $id
     * @param  string $value
     * @param  array $params  Parameters to use for dijit creation
     * @param  array $attribs HTML attributes
     * @return string
     */
    public function __invoke($id = null, $value = null, array $params = array(), array $attribs = array())
    {
        if (!array_key_exists('label', $params)) {
            $params['label'] = $value;
        }
        if (empty($params['label']) && !empty($params['content'])) {
            $params['label'] = $params['content'];
            $value = $params['content'];
        }
        if (empty($params['label']) && !empty($attribs['content'])) {
            $params['label'] = $attribs['content'];
            $value = $attribs['content'];
            unset($attribs['content']);
        }
        return $this->_createFormElement($id, $value, $params, $attribs);
    }
}
