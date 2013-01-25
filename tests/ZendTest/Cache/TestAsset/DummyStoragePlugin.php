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

use Zend\Cache\Storage\Plugin\AbstractPlugin;

class DummyStoragePlugin extends AbstractPlugin
{

    /**
     * Overwrite constructor: do not check internal storage
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }
}
