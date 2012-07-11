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
 * Dojo NumberSpinner dijit
 *
 * @package    Zend_Dojo
 * @subpackage View
 */
class NumberSpinner extends Dijit
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dijit.form.NumberSpinner';

    /**
     * HTML element type
     * @var string
     */
    protected $_elementType = 'text';

    /**
     * Dojo module to use
     * @var string
     */
    protected $_module = 'dijit.form.NumberSpinner';

    /**
     * dijit.form.NumberSpinner
     *
     * @param  int $id
     * @param  mixed $value
     * @param  array $params  Parameters to use for dijit creation
     * @param  array $attribs HTML attributes
     * @return string
     */
    public function __invoke($id = null, $value = null, array $params = array(), array $attribs = array())
    {
        // Get constraints and serialize to JSON if necessary
        if (array_key_exists('constraints', $params)) {
            if (!is_array($params['constraints'])) {
                unset($params['constraints']);
            }
        } else {
            $constraints = array();
            if (array_key_exists('min', $params)) {
                $constraints['min'] = $params['min'];
                unset($params['min']);
            }
            if (array_key_exists('max', $params)) {
                $constraints['max'] = $params['max'];
                unset($params['max']);
            }
            if (array_key_exists('places', $params)) {
                $constraints['places'] = $params['places'];
                unset($params['places']);
            }
            $params['constraints'] = $constraints;
        }

        return $this->_createFormElement($id, $value, $params, $attribs);
    }
}
