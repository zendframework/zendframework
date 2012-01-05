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
 * @package    Zend_Controller
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Response;

/**
 * Zend_Controller_Response_Cli
 *
 * CLI response for controllers
 *
 * @uses       \Zend\Controller\Response\AbstractResponse
 * @package    Zend_Controller
 * @subpackage Response
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Cli extends AbstractResponse
{
    /**
     * Flag; if true, when header operations are called after headers have been
     * sent, an exception will be raised; otherwise, processing will continue
     * as normal. Defaults to false.
     *
     * @see canSendHeaders()
     * @var boolean
     */
    public $headersSentThrowsException = false;


    /**
     * Magic __toString functionality
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->isException() && $this->renderExceptions()) {
            $exceptions = '';
            foreach ($this->getException() as $e) {
                $exceptions .= $e->__toString() . "\n";
            }
            return $exceptions;
        }

        return $this->_body;
    }
}
