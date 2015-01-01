<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Json
 */

namespace ZendTest\Json\TestAsset;

use JsonSerializable;

/**
 * Implementation of the built-in JsonSerializable interface.
 *
 * This asset will not work in PHP <5.4.0.
 */
class JsonSerializableBuiltinImpl implements JsonSerializable
{
    public function jsonSerialize()
    {
        return array(__FUNCTION__);
    }
}
