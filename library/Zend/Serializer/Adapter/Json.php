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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Serializer\Adapter;

use Zend\Serializer\Exception as SerializationException,
    Zend\Json\Json as ZendJson;

/**
 * @uses       Zend\Json\Json
 * @uses       Zend\Serializer\Adapter\AbstractAdapter
 * @uses       Zend\Serializer\Exception
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
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
     * @throws \Zend\Serializer\Exception on JSON encoding exception
     */
    public function serialize($value, array $opts = array())
    {
        $opts = $opts + $this->_options;

        try  {
            return ZendJson::encode($value, $opts['cycleCheck'], $opts);
        } catch (\Exception $e) {
            throw new SerializationException('Serialization failed', 0, $e);
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
        } catch (\Exception $e) {
            throw new SerializationException('Unserialization failed by previous error', 0, $e);
        }

        // json_decode returns null for invalid JSON
        if ($ret === null && $json !== 'null') {
            throw new SerializationException('Invalid json data');
        }

        return $ret;
    }
}
