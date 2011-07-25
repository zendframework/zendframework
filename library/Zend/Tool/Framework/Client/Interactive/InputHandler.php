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
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Framework\Client\Interactive;

use Zend\Tool\Framework\Client\Exception;

/**
 * @uses       \Zend\Tool\Framework\Client\Exception
 * @uses       \Zend\Tool\Framework\Client\Interactive\InputRequest
 * @uses       \Zend\Tool\Framework\Client\Interactive\InputResponse
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class InputHandler
{

    /**
     * @var \Zend\Tool\Framework\Client\Interactive\InteractiveInput
     */
    protected $_client = null;

    protected $_inputRequest = null;

    public function setClient(InteractiveInput $client)
    {
        $this->_client = $client;
        return $this;
    }

    public function setInputRequest($inputRequest)
    {
        if (is_string($inputRequest)) {
            $inputRequest = new InputRequest($inputRequest);
        } elseif (!$inputRequest instanceof InputRequest) {
            throw new Exception\InvalidArgumentException('promptInteractive() requires either a string or an instance of Zend\Tool\Framework\Client\Interactive\InputRequest.');
        }

        $this->_inputRequest = $inputRequest;
        return $this;
    }

    public function handle()
    {
        $inputResponse = $this->_client->handleInteractiveInputRequest($this->_inputRequest);

        if (is_string($inputResponse)) {
            $inputResponse = new InputResponse($inputResponse);
        } elseif (!$inputResponse instanceof InputResponse) {
            throw new Exception\InvalidArgumentException('The registered $_interactiveCallback for the client must either return a string or an instance of Zend\Tool\Framework\Client\Interactive\InputResponse.');
        }

        return $inputResponse;
    }


}
