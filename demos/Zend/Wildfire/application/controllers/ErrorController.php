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
 * @package    Zend_Wildfire
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * A sample error controller.
 *
 * @category   Zend
 * @package    Zend_Wildfire
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        /*
         * Make sure we don't log exceptions thrown during the exception logging.
         * If we do we will create an infinite loop!
         */

        try {

            Zend_Registry::get('logger')->err($this->_getParam('error_handler')->exception);

        } catch(Exception $e) {

          /* TODO: You can log this exception somewhere or display it during development.
           *       DO NOT USE THE logger here as it will create an infinite loop!
           */

        }
    }
}

