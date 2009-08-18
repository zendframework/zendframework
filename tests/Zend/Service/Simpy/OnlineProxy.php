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
 * @package    Zend_Service_Simpy
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'BaseProxy.php';

/**
 * @category   Zend
 * @package    Zend_Service_Simpy
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Simpy_OnlineProxy extends Zend_Service_Simpy_BaseProxy
{
    /**
     * Proxy all method calls to the service consumer object and write 
     * responses to local files, regardless of whether service calls result 
     * in an exception being thrown.
     *
     * @param string $name Name of the method called
     * @param array $args Arguments passed in the method call
     * @return mixed Return value of the called method
     */
    public function __call($name, array $args)
    {
        sleep(3);

        try {
            $return = call_user_func_array(
                array($this->_simpy, $name),
                $args
            );
        } catch (Exception $e) { }

        $response = $this->_simpy
            ->getHttpClient()
            ->getLastResponse()
            ->getBody();

        $file = $this->_getFilePath($name);

        file_put_contents($file, $response);

        if (isset($e)) {
            throw $e;
        }

        return $return;
    }
}
