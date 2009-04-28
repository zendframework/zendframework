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
 * This class forms part of a proposal for the Zend Framework. The attached
 * copyright will be transferred to Zend Technologies USA Inc. upon future
 * acceptance of that proposal:
 *      http://framework.zend.com/wiki/pages/viewpage.action?pageId=20369
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage Math
 * @copyright  Copyright (c) 2007 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Crypt_Math_BigInteger_Interface */
require_once 'Zend/Crypt/Math/BigInteger/Interface.php';

/**
 * Support for arbitrary precision mathematics in PHP.
 *
 * Zend_Crypt_Math_BigInteger is a wrapper across three PHP extensions: bcmath, gmp
 * and big_int. Since each offer similar functionality, but availability of
 * each differs across installations of PHP, this wrapper attempts to select
 * the fastest option available and encapsulate a subset of its functionality
 * which all extensions share in common.
 *
 * This class requires one of the three extensions to be available. BCMATH
 * while the slowest, is available by default under Windows, and under Unix
 * if PHP is compiled with the flag "--enable-bcmath". GMP requires the gmp
 * library from http://www.swox.com/gmp/ and PHP compiled with the "--with-gmp"
 * flag. BIG_INT support is available from a big_int PHP library available from
 * only from PECL (a Windows port is not available).
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage Math
 * @author     Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Crypt_Math_BigInteger
{

    /**
     * Holds an instance of one of the three arbitrary precision wrappers.
     *
     * @var Zend_Math_BigInteger_Interface
     */
    protected $_math = null;

    /**
     * Constructor; a Factory which detects a suitable PHP extension for
     * arbitrary precision math and instantiates the suitable wrapper
     * object.
     *
     * @throws  Zend_Math_BigInteger_Exception
     */
    public function __construct($extension = null)
    {
        if (!is_null($extension) && !in_array($extension, array('bcmath', 'gmp', 'bigint'))) {
            require_once('Zend/Crypt/Math/BigInteger/Exception.php');
            throw new Zend_Crypt_Math_BigInteger_Exception('Invalid extension type; please use one of bcmath, gmp or bigint');
        }
        $this->_loadAdapter($extension);
    }

    /**
     * Redirect all public method calls to the wrapped extension object.
     *
     * @param   string $methodName
     * @param   array $args
     * @throws  Zend_Crypt_Math_BigInteger_Exception
     */
    public function __call($methodName, $args)
    {
        if(!method_exists($this->_math, $methodName)) {
            require_once 'Zend/Crypt/Math/BigInteger/Exception.php';
            throw new Zend_Crypt_Math_BigInteger_Exception('invalid method call: ' . get_class($this->_math) . '::' . $methodName . '() does not exist');
        }
        return call_user_func_array(array($this->_math, $methodName), $args);
    }

    protected function _loadAdapter($extension = null)
    {
        if (is_null($extension)) {
            if (extension_loaded('gmp')) {
                $extension = 'gmp';
            //} elseif (extension_loaded('big_int')) {
            //    $extension = 'big_int';
            } else {
                $extension = 'bcmath';
            }
        }
        if($extension == 'gmp' && extension_loaded('gmp')) {
            require_once 'Zend/Crypt/Math/BigInteger/Gmp.php';
            $this->_math = new Zend_Crypt_Math_BigInteger_Gmp();
        //} elseif($extension == 'bigint' && extension_loaded('big_int')) {
        //    require_once 'Zend/Crypt_Math/BigInteger/Bigint.php';
        //    $this->_math = new Zend_Crypt_Math_BigInteger_Bigint();
        } elseif ($extension == 'bcmath') {
            require_once 'Zend/Crypt/Math/BigInteger/Bcmath.php';
            $this->_math = new Zend_Crypt_Math_BigInteger_Bcmath();
        } else {
            require_once 'Zend/Crypt/Math/BigInteger/Exception.php';
            throw new Zend_Crypt_Math_BigInteger_Exception($extension . ' big integer precision math support not detected');
        }
    }

}