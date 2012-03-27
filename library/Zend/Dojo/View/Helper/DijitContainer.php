<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Dojo\View\Helper;

use Zend\Dojo\View\Exception;

/**
 * Dijit layout container base class
 *
 * @uses       \Zend\Dojo\View\Exception
 * @uses       \Zend\Dojo\View\Helper\Dijit
 * @package    Zend_Dojo
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */
abstract class DijitContainer extends Dijit
{
    /**
     * Capture locks
     * @var array
     */
    protected $_captureLock = array();

    /**
     * Metadata information to use with captured content
     * @var array
     */
    protected $_captureInfo = array();

    /**
     * Begin capturing content for layout container
     *
     * @param  string $id
     * @param  array $params
     * @param  array $attribs
     * @return void
     */
    public function captureStart($id, array $params = array(), array $attribs = array())
    {
        if (array_key_exists($id, $this->_captureLock)) {
            throw new Exception\RuntimeException(sprintf('Lock already exists for id "%s"', $id));
        }

        $this->_captureLock[$id] = true;
        $this->_captureInfo[$id] = array(
            'params'  => $params,
            'attribs' => $attribs,
        );

        ob_start();
        return;
    }

    /**
     * Finish capturing content for layout container
     *
     * @param  string $id
     * @return string
     */
    public function captureEnd($id)
    {
        if (!array_key_exists($id, $this->_captureLock)) {
            throw new Exception\RuntimeException(sprintf('No capture lock exists for id "%s"; nothing to capture', $id));
        }

        $content = ob_get_clean();
        extract($this->_captureInfo[$id]);
        unset($this->_captureLock[$id], $this->_captureInfo[$id]);
        return $this->_createLayoutContainer($id, $content, $params, $attribs);
    }
}
