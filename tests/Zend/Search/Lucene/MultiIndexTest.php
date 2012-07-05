<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Search\Lucene;

use Zend\Search\Lucene;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class MultiIndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Zend\Search\Lucene\MultiSearcher::find
     * @covers Zend\Search\Lucene\Search\QueryHit::getDocument
     */
    public function testFind()
    {
        $index = new Lucene\MultiSearcher(array(
                Lucene\Lucene::open(__DIR__ . '/_indexSample/_files'),
                Lucene\Lucene::open(__DIR__ . '/_indexSample/_files'),
        ));

        $hits = $index->find('submitting');
        $this->assertEquals(count($hits), 2*3);
        foreach($hits as $hit) {
            $document = $hit->getDocument();
            $this->assertTrue($document instanceof Lucene\Document);
        }
    }
}
