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
 * @package    Zend_Gdata_Analytics
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData\Analytics;

use Zend\GData\Analytics\DataEntry;
use Zend\GData\Analytics\DataFeed;
use Zend\GData\Analytics\DataQuery;

/**
 * @category   Zend
 * @package    Zend_Gdata_Analytics
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Analytics
 */
class Zend_Gdata_Analytics_DataFeedTest extends \PHPUnit_Framework_TestCase
{
    public $testData = array(
        'blogger.com' => 68140,
        'google.com'  => 29666,
        'stumbleupon.com' => 4012,
        'google.co.uk' => 2968,
        'google.co.in' => 2793,
    );
    /** @var DataFeed */
    public $dataFeed;

    public function setUp()
    {
        $this->dataFeed = new DataFeed(
            file_get_contents(dirname(__FILE__) . '/_files/TestDataFeed.xml'),
            true
        );
    }

    public function testDataFeed()
    {
        $count = count($this->testData);
        $this->assertEquals(count($this->dataFeed->entries), $count);
        $this->assertEquals($this->dataFeed->entries->count(), $count);
        foreach ($this->dataFeed->entries as $entry) {
            $this->assertTrue($entry instanceof DataEntry);
        }
    }

    public function testGetters()
    {
        $sources = array_keys($this->testData);
        $values = array_values($this->testData);

        foreach ($this->dataFeed as $index => $row) {
            $source = $row->getDimension(DataQuery::DIMENSION_SOURCE);
            $medium = $row->getDimension('ga:medium');
            $visits = $row->getMetric('ga:visits');
            $visitsValue = $row->getValue('ga:visits');

            $this->assertEquals("$medium", 'referral');
            $this->assertEquals("$source", $sources[$index]);
            $this->assertEquals("$visits", $values[$index]);
            $this->assertEquals("$visitsValue", $values[$index]);
        }
    }
}
