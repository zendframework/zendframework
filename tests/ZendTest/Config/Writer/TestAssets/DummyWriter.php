<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Config\Writer\TestAssets;

use Zend\Config\Writer\AbstractWriter;

class DummyWriter extends AbstractWriter
{
    public function processConfig(array $config)
    {
        return serialize($config);
    }
}
