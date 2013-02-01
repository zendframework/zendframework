<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace ZendTest\Paginator\TestAsset;

use Zend\Paginator;
use Zend\Paginator\Adapter;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 */
class TestArrayAggregate implements Paginator\AdapterAggregateInterface
{
    public function getPaginatorAdapter()
    {
        return new Adapter\ArrayAdapter(array(1, 2, 3, 4));
    }
}
