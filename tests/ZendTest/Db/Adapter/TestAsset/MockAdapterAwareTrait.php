<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace ZendTest\Db\Adapter\TestAsset;

use \Zend\Db\Adapter\AdapterAwareTrait;
use \Zend\Db\Adapter\AdapterAwareInterface;

class MockAdapterAwareTrait implements AdapterAwareInterface
{
    use AdapterAwareTrait;
}
