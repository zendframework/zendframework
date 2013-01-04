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

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 */
class TestAdapter extends \ArrayObject implements \Zend\Paginator\Adapter\AdapterInterface
{
    public function count()
    {
        return 10;
    }

    public function getItems($pageNumber, $itemCountPerPage)
    {
        return new \ArrayObject(range(1, 10));
    }
}
