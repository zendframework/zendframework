<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Serializer
 */

namespace Zend\Serializer\Adapter;

use Zend\Json\Json as ZendJson;
use Zend\Serializer\Exception\InvalidArgumentException;
use Zend\Serializer\Exception\RuntimeException;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
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
     * @throws InvalidArgumentException|RuntimeException on JSON encoding exception
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
     * @throws InvalidArgumentException|RuntimeException
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
