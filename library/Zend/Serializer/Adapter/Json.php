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
use Zend\Serializer\Exception;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 */
class Json implements AdapterInterface
{
    /**
     * @var JsonOptions
     */
    protected $options = null;

    /**
     * Constructor.
     *
     * @param  JsonOptions $options Optional
     */
    public function __construct(JsonOptions $options = null)
    {
        if ($options === null) {
            $options = new JsonOptions();;
        }
        $this->options = $options;
    }

    /**
     * Set options
     *
     * @param  JsonOptions $options
     * @return Json
     */
    public function setOptions(JsonOptions $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Get options
     *
     * @return JsonOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Serialize PHP value to JSON
     *
     * @param  mixed $value
     * @return string
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function serialize($value)
    {
        $cycleCheck = $this->options->getCycleCheck();
        $opts = array(
            'enableJsonExprFinder' => $this->options->getEnableJsonExprFinder(),
            'objectDecodeType'     => $this->options->getObjectDecodeType(),
        );

        try  {
            return ZendJson::encode($value, $cycleCheck, $opts);
        } catch (\InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException('Serialization failed: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            throw new Exception\RuntimeException('Serialization failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Deserialize JSON to PHP value
     *
     * @param  string $json
     * @return mixed
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function unserialize($json)
    {
        try {
            $ret = ZendJson::decode($json, $this->options->getObjectDecodeType());
        } catch (\InvalidArgumentException $e) {
            throw new Exception\InvalidArgumentException('Unserialization failed: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            throw new Exception\RuntimeException('Unserialization failed: ' . $e->getMessage(), 0, $e);
        }

        return $ret;
    }
}