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
 * Dojo FilteringSelect dijit
 *
 * @package    Zend_Dojo
 * @subpackage View
  */
class FilteringSelect extends ComboBox
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dijit.form.FilteringSelect';

    /**
     * Dojo module to use
     * @var string
     */
    protected $_module = 'dijit.form.FilteringSelect';

    /**
     * dijit.form.FilteringSelect
     *
     * @param  int $id
     * @param  mixed $value
     * @param  array $params  Parameters to use for dijit creation
     * @param  array $attribs HTML attributes
     * @param  array|null $options Select options
     * @return string
     */
    public function __invoke($id = null, $value = null, array $params = array(), array $attribs = array(), array $options = null)
    {
        return parent::__invoke($id, $value, $params, $attribs, $options);
    }
}
