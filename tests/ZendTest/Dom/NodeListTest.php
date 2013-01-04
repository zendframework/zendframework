<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dom
 */

namespace ZendTest\Dom;

use Zend\Dom\NodeList;

/**
 * @category   Zend
 * @package    Zend_Dom
 * @subpackage UnitTests
 * @group      Zend_Dom
 */
class NodeListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group ZF-4631
     */
    public function testEmptyResultDoesNotReturnIteratorValidTrue()
    {
        $dom = new \DOMDocument();
        $emptyNodeList = $dom->getElementsByTagName("a");
        $result = new NodeList("", "", $dom, $emptyNodeList);

        $this->assertFalse($result->valid());
    }
}
