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

use Zend\Dojo\View\Exception;

/**
 * Arbitrary dijit support
 *
 * @package    Zend_Dojo
 * @subpackage View
  */
class CustomDijit extends DijitContainer
{
    /**
     * Default dojoType; set the value when extending
     * @var string
     */
    protected $_defaultDojoType;

    /**
     * Render a custom dijit
     *
     * Requires that either the {@link $_defaultDojotype} property is set, or
     * that you pass a value to the "dojoType" key of the $params argument.
     *
     * @param  string $id
     * @param  string $value
     * @param  array $params
     * @param  array $attribs
     * @return string|\Zend\Dojo\View\Helper\CustomDijit
     */
    public function __invoke($id = null, $value = null, array $params = array(), array $attribs = array())
    {
        if (null === $id) {
            return $this;
        }

        if (!array_key_exists('dojoType', $params)
            && (null === $this->_defaultDojoType)
        ) {
            throw new Exception\InvalidArgumentException('No dojoType specified; cannot create dijit');
        } elseif (array_key_exists('dojoType', $params)) {
            $this->_dijit  = $params['dojoType'];
            $this->_module = $params['dojoType'];
            unset($params['dojoType']);
        } else {
            $this->_dijit  = $this->_defaultDojoType;
            $this->_module = $this->_defaultDojoType;
        }

        if (array_key_exists('rootNode', $params)) {
            $this->setRootNode($params['rootNode']);
            unset($params['rootNode']);
        }

        return $this->_createLayoutContainer($id, $value, $params, $attribs);
    }

    /**
     * Begin capturing content.
     *
     * Requires that either the {@link $_defaultDojotype} property is set, or
     * that you pass a value to the "dojoType" key of the $params argument.
     *
     * @param  string $id
     * @param  array $params
     * @param  array $attribs
     * @return void
     */
    public function captureStart($id, array $params = array(), array $attribs = array())
    {
        if (!array_key_exists('dojoType', $params)
            && (null === $this->_defaultDojoType)
        ) {
            throw new Exception\InvalidArgumentException('No dojoType specified; cannot create dijit');
        } elseif (array_key_exists('dojoType', $params)) {
            $this->_dijit  = $params['dojoType'];
            $this->_module = $params['dojoType'];
            unset($params['dojoType']);
        } else {
            $this->_dijit  = $this->_defaultDojoType;
            $this->_module = $this->_defaultDojoType;
        }

        return parent::captureStart($id, $params, $attribs);
    }
}
