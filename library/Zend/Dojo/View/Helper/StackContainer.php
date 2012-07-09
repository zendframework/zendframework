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
 * Dojo StackContainer dijit
 *
 * @package    Zend_Dojo
 * @subpackage View
  */
class StackContainer extends DijitContainer
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dijit.layout.StackContainer';

    /**
     * Dojo module to use
     * @var string
     */
    protected $_module = 'dijit.layout.StackContainer';

    /**
     * dijit.layout.StackContainer
     *
     * @param  string $id
     * @param  string $content
     * @param  array $params  Parameters to use for dijit creation
     * @param  array $attribs HTML attributes
     * @return string
     */
    public function __invoke($id = null, $content = '', array $params = array(), array $attribs = array())
    {
        if (0 === func_num_args()) {
            return $this;
        }

        return $this->_createLayoutContainer($id, $content, $params, $attribs);
    }
}
