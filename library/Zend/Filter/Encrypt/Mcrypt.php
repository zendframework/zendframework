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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Filter\Encrypt;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Filter\Exception,
    Zend\Filter\Compress,
    Zend\Filter\Decompress;

/**
 * Encryption adapter for mcrypt
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Mcrypt implements EncryptionAlgorithmInterface
{
    /**
     * Definitions for encryption
     * array(
     *     'key' => encryption key string
     *     'algorithm' => algorithm to use
     *     'algorithm_directory' => directory where to find the algorithm
     *     'mode' => encryption mode to use
     *     'modedirectory' => directory where to find the mode
     * )
     */
    protected $_encryption = array(
        'key'                 => 'ZendFramework',
        'algorithm'           => 'blowfish',
        'algorithm_directory' => '',
        'mode'                => 'cbc',
        'mode_directory'      => '',
        'vector'              => null,
        'salt'                => false,
    );

    /**
     * Internal compression
     *
     * @var array
     */
    protected $_compression;

    /**
     * Class constructor
     *
     * @param string|array|\Traversable $options Encryption Options
     */
    public function __construct($options)
    {
        if (!extension_loaded('mcrypt')) {
            throw new Exception\ExtensionNotLoadedException('This filter needs the mcrypt extension');
        }

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (is_string($options)) {
            $options = array('key' => $options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException('Invalid options argument provided to filter');
        }

        if (array_key_exists('compression', $options)) {
            $this->setCompression($options['compression']);
            unset($options['compress']);
        }

        if (array_key_exists('compression', $options)) {
            $this->setCompression($options['compression']);
            unset($options['compress']);
        }

        $this->setEncryption($options);
    }

    /**
     * Returns the set encryption options
     *
     * @return array
     */
    public function getEncryption()
    {
        return $this->_encryption;
    }

    /**
     * Sets new encryption options
     *
     * @param  string|array $options Encryption options
     * @return Zend_Filter_File_Encryption
     */
    public function setEncryption($options)
    {
        if (is_string($options)) {
            $options = array('key' => $options);
        }

        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException('Invalid options argument provided to filter');
        }

        $options = $options + $this->getEncryption();
        $algorithms = mcrypt_list_algorithms($options['algorithm_directory']);
        if (!in_array($options['algorithm'], $algorithms)) {
            throw new Exception\InvalidArgumentException("The algorithm '{$options['algorithm']}' is not supported");
        }

        $modes = mcrypt_list_modes($options['mode_directory']);
        if (!in_array($options['mode'], $modes)) {
            throw new Exception\InvalidArgumentException("The mode '{$options['mode']}' is not supported");
        }

        if (!mcrypt_module_self_test($options['algorithm'], $options['algorithm_directory'])) {
            throw new Exception\InvalidArgumentException('The given algorithm can not be used due an internal mcrypt problem');
        }

        if (!isset($options['vector'])) {
            $options['vector'] = null;
        }

        $this->_encryption = $options;
        $this->setVector($options['vector']);

        return $this;
    }

    /**
     * Returns the set vector
     *
     * @return string
     */
    public function getVector()
    {
        return $this->_encryption['vector'];
    }

    /**
     * Sets the initialization vector
     *
     * @param string $vector (Optional) Vector to set
     * @return \Zend\Filter\Encrypt\Mcrypt
     */
    public function setVector($vector = null)
    {
        $cipher = $this->_openCipher();
        $size   = mcrypt_enc_get_iv_size($cipher);
        if (empty($vector)) {
            if (file_exists('/dev/urandom') || (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')) {
                $method = MCRYPT_DEV_URANDOM;
            } elseif (file_exists('/dev/random')) {
                $method = MCRYPT_DEV_RANDOM;
            } else {
                $method = MCRYPT_RAND;
            }

            $vector = mcrypt_create_iv($size, $method);
        } else if (strlen($vector) != $size) {
            throw new Exception\InvalidArgumentException('The given vector has a wrong size for the set algorithm');
        }

        $this->_encryption['vector'] = $vector;
        $this->_closeCipher($cipher);

        return $this;
    }

    /**
     * Returns the compression
     *
     * @return array
     */
    public function getCompression()
    {
        return $this->_compression;
    }

    /**
     * Sets a internal compression for values to encrypt
     *
     * @param string|array $compression
     * @return \Zend\Filter\Encrypt\Mcrypt
     */
    public function setCompression($compression)
    {
        if (is_string($this->_compression)) {
            $compression = array('adapter' => $compression);
        }

        $this->_compression = $compression;
        return $this;
    }

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * Encrypts $value with the defined settings
     *
     * @param  string $value The content to encrypt
     * @return string The encrypted content
     */
    public function encrypt($value)
    {
        // compress prior to encryption
        if (!empty($this->_compression)) {
            $compress = new Compress($this->_compression);
            $value    = $compress($value);
        }

        $cipher  = $this->_openCipher();
        $this->_initCipher($cipher);
        $encrypted = mcrypt_generic($cipher, $value);
        mcrypt_generic_deinit($cipher);
        $this->_closeCipher($cipher);

        return $encrypted;
    }

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * Decrypts $value with the defined settings
     *
     * @param  string $value Content to decrypt
     * @return string The decrypted content
     */
    public function decrypt($value)
    {
        $cipher = $this->_openCipher();
        $this->_initCipher($cipher);
        $decrypted = mdecrypt_generic($cipher, $value);
        mcrypt_generic_deinit($cipher);
        $this->_closeCipher($cipher);

        // decompress after decryption
        if (!empty($this->_compression)) {
            $decompress = new Decompress($this->_compression);
            $decrypted  = $decompress($decrypted);
        }

        return $decrypted;
    }

    /**
     * Returns the adapter name
     *
     * @return string
     */
    public function toString()
    {
        return 'Mcrypt';
    }

    /**
     * Open a cipher
     *
     * @throws Exception\RuntimeException When the cipher can not be opened
     * @return resource Returns the opened cipher
     */
    protected function _openCipher()
    {
        $cipher = mcrypt_module_open(
            $this->_encryption['algorithm'],
            $this->_encryption['algorithm_directory'],
            $this->_encryption['mode'],
            $this->_encryption['mode_directory']);

        if ($cipher === false) {
            throw new Exception\RuntimeException('Mcrypt can not be opened with your settings');
        }

        return $cipher;
    }

    /**
     * Close a cipher
     *
     * @param  resource $cipher Cipher to close
     * @return \Zend\Filter\Encrypt\Mcrypt
     */
    protected function _closeCipher($cipher)
    {
        mcrypt_module_close($cipher);
        return $this;
    }

    /**
     * Initialises the cipher with the set key
     *
     * @param  resource $cipher
     * @throws
     * @return resource
     */
    protected function _initCipher($cipher)
    {
        $key = $this->_encryption['key'];

        $keysizes = mcrypt_enc_get_supported_key_sizes($cipher);
        if (empty($keysizes) || ($this->_encryption['salt'] == true)) {
            $keysize = mcrypt_enc_get_key_size($cipher);
            $key     = substr(md5($key), 0, $keysize);
        } else if (!in_array(strlen($key), $keysizes)) {
            throw new Exception\RuntimeException('The given key has a wrong size for the set algorithm');
        }

        $result = mcrypt_generic_init($cipher, $key, $this->_encryption['vector']);
        if ($result < 0) {
            throw new Exception\RuntimeException('Mcrypt could not be initialize with the given setting');
        }

        return $this;
    }
}
