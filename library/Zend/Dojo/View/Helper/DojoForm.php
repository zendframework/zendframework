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

use Zend\View\Helper\Form as FormHelper;

/**
 * Dojo Form dijit
 *
 * @package    Zend_Dojo
 * @subpackage View
 */
class DojoForm extends Dijit
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dijit.form.Form';

    /**
     * Module being used
     * @var string
     */
    protected $_module = 'dijit.form.Form';

    /**
     * @var \Zend\View\Helper\Form
     */
    protected $_helper;

    /**
     * dijit.form.Form
     *
     * @param  string $id
     * @param  null|array $attribs HTML attributes
     * @param  false|string $content
     * @return string
     */
    public function __invoke($id = null, $attribs = null, $content = false)
    {
        if (!is_array($attribs)) {
            $attribs = (array) $attribs;
        }
        if (array_key_exists('id', $attribs)) {
            $attribs['name'] = $id;
        } else {
            $attribs['id'] = $id;
        }

        $attribs = $this->_prepareDijit($attribs, array(), 'layout');

        $formHelper = $this->getFormHelper();
        return $formHelper($id, $attribs, $content);
    }

    /**
     * Get standard form helper
     *
     * @return \Zend\View\Helper\Form
     */
    public function getFormHelper()
    {
        if (null === $this->_helper) {
            $this->_helper = new FormHelper();
            $this->_helper->setView($this->view);
        }
        return $this->_helper;
    }
}
