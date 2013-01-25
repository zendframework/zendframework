<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\TestAsset;

use Zend\Cache;

class DummyPattern extends Cache\Pattern\AbstractPattern
{

    public $_dummyOption = 'dummyOption';

    public function setDummyOption($value)
    {
        $this->_dummyOption = $value;
        return $this;
    }

    public function getDummyOption()
    {
        return $this->_dummyOption;
    }
}
