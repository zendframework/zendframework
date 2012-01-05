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

/**
 * @namespace
 */
namespace Zend\Serializer\Adapter;

use Zend\Serializer\Exception\InvalidArgumentException,
    Zend\Serializer\Exception\RuntimeException,
    Zend\Json\Json as ZendJson;

/**
 * @uses       Zend\Serializer\Adapter\AbstractAdapter
 * @uses       Zend\Serializer\Exception\InvalidArgumentException
 * @uses       Zend\Serializer\Exception\RuntimeException
 * @uses       Zend\Json\Json
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Json extends AbstractAdapter
{
    /**
     * @var array Default options
     */
    protected $_options = array(
        'cycleCheck'           => false,
        'enableJsonExprFinder' => false,
        'objectDecodeType'     => ZendJson::TYPE_ARRAY,
    );

    /**
     * Serialize PHP value to JSON
     * 
     * @param  mixed $value 
     * @param  array $opts 
     * @return string
     * @throws Zend\Serializer\Exception on JSON encoding exception
     */
    public function serialize($value, array $opts = array())
    {
        $opts = $opts + $this->_options;

        try  {
            return ZendJson::encode($value, $opts['cycleCheck'], $opts);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidArgumentException('Serialization failed: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            throw new RuntimeException('Serialization failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Deserialize JSON to PHP value
     * 
     * @param  string $json 
     * @param  array $opts 
     * @return mixed
     */
    public function unserialize($json, array $opts = array())
    {
        $opts = $opts + $this->_options;

        try {
            $ret = ZendJson::decode($json, $opts['objectDecodeType']);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidArgumentException('Unserialization failed: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            throw new RuntimeException('Unserialization failed: ' . $e->getMessage(), 0, $e);
        }

        return $ret;
    }
}
