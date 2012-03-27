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
 * @subpackage Zend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Controller\Action\Helper;

use Zend\Layout\Layout;

/**
 * Create and send autocompletion lists
 *
 * @uses       \Zend\Controller\Action\Exception
 * @uses       \Zend\Controller\Action\Helper\AbstractHelper
 * @uses       \Zend\Layout\Layout
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Zend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractAutoComplete extends AbstractHelper
{
    /**
     * Suppress exit when sendJson() called
     *
     * @var boolean
     */
    public $suppressExit = false;

    /**
     * Validate autocompletion data
     *
     * @param  mixed $data
     * @return boolean
     */
    abstract public function validateData($data);

    /**
     * Prepare autocompletion data
     *
     * @param  mixed   $data
     * @param  boolean $keepLayouts
     * @return mixed
     */
    abstract public function prepareAutoCompletion($data, $keepLayouts = false);

    /**
     * Disable layouts and view renderer
     *
     * @return \Zend\Controller\Action\Helper\AutoComplete\AbstractAutoComplete Provides a fluent interface
     */
    public function disableLayouts()
    {
        if (null !== ($layout = Layout::getMvcInstance())) {
            $layout->disableLayout();
        }

        $this->getBroker()->load('viewRenderer')->setNoRender(true);

        return $this;
    }

    /**
     * Encode data to JSON
     *
     * @param  mixed $data
     * @param  bool  $keepLayouts
     * @throws \Zend\Controller\Action\Exception
     * @return string
     */
    public function encodeJson($data, $keepLayouts = false)
    {
        if ($this->validateData($data)) {
            return $this->getBroker()->load('json')->encodeJson($data, $keepLayouts);
        }

        throw new \Zend\Controller\Action\Exception('Invalid data passed for autocompletion');
    }

    /**
     * Send autocompletion data
     *
     * Calls prepareAutoCompletion, populates response body with this
     * information, and sends response.
     *
     * @param  mixed $data
     * @param  bool  $keepLayouts
     * @return string|void
     */
    public function sendAutoCompletion($data, $keepLayouts = false)
    {
        $data = $this->prepareAutoCompletion($data, $keepLayouts);

        $response = $this->getResponse();
        $response->setBody($data);

        if (!$this->suppressExit) {
            $response->sendResponse();
            exit;
        }

        return $data;
    }

    /**
     * Strategy pattern: allow calling helper as broker method
     *
     * Prepares autocompletion data and, if $sendNow is true, immediately sends
     * response.
     *
     * @param  mixed $data
     * @param  bool  $sendNow
     * @param  bool  $keepLayouts
     * @return string|void
     */
    public function direct($data, $sendNow = true, $keepLayouts = false)
    {
        if ($sendNow) {
            return $this->sendAutoCompletion($data, $keepLayouts);
        }

        return $this->prepareAutoCompletion($data, $keepLayouts);
    }
}
