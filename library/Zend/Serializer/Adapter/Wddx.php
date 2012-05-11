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
 * @package    Zend_Serializer
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Serializer\Adapter;

use Zend\Serializer\Exception\RuntimeException,
    Zend\Serializer\Exception\ExtensionNotLoadedException;

/**
 * @link       http://www.infoloom.com/gcaconfs/WEB/chicago98/simeonov.HTM
 * @link       http://en.wikipedia.org/wiki/WDDX
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wddx extends AbstractAdapter
{
    /**
     * @var array Default options
     */
    protected $_options = array(
        'comment' => null,
    );

    /**
     * Constructor
     * 
     * @param  array $options
     * @return void
     * @throws ExtensionNotLoadedException if wddx extension not found
     */
    public function __construct($options = array())
    {
        if (!extension_loaded('wddx')) {
            throw new ExtensionNotLoadedException('PHP extension "wddx" is required for this adapter');
        }

        parent::__construct($options);
    }

    /**
     * Serialize PHP to WDDX
     * 
     * @param  mixed $value 
     * @param  array $opts 
     * @return string
     * @throws RuntimeException on wddx error
     */
    public function serialize($value, array $opts = array())
    {
        $opts = $opts + $this->_options;

        if (isset($opts['comment']) && $opts['comment']) {
            $wddx = wddx_serialize_value($value, (string)$opts['comment']);
        } else {
            $wddx = wddx_serialize_value($value);
        }

        if ($wddx === false) {
            $lastErr = error_get_last();
            throw new RuntimeException($lastErr['message']);
        }
        return $wddx;
    }

    /**
     * Unserialize from WDDX to PHP
     * 
     * @param  string $wddx 
     * @param  array $opts 
     * @return mixed
     * @throws RuntimeException on wddx error
     */
    public function unserialize($wddx, array $opts = array())
    {
        $ret = wddx_deserialize($wddx);

        if ($ret === null && class_exists('SimpleXMLElement', false)) {
            // check if the returned NULL is valid
            // or based on an invalid wddx string
            try {
                $simpleXml = new \SimpleXMLElement($wddx);
                if (isset($simpleXml->data[0]->null[0])) {
                    return null; // valid null
                }
                throw new RuntimeException('Invalid wddx');
            } catch (\Exception $e) {
                throw new RuntimeException($e->getMessage(), 0, $e);
            }
        }

        return $ret;
    }
}
