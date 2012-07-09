<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\Analytics;

use Zend\GData\Analytics\AccountFeed;

/**
 * @category   Zend
 * @package    Zend_Gdata_Analytics
 * @subpackage UnitTests
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Analytics
 */
class AccountFeedTest extends \PHPUnit_Framework_TestCase
{

    /** @var AccountFeed */
    public $accountFeed;

    public function setUp()
    {
        $this->accountFeed = new AccountFeed(
            file_get_contents(dirname(__FILE__) . '/_files/TestAccountFeed.xml'),
            true
        );
    }

    public function testAccountFeed()
    {
        $this->assertEquals(3, count($this->accountFeed->entries));
        $this->assertEquals(3, $this->accountFeed->entries->count());
        foreach ($this->accountFeed->entries as $entry) {
            $this->assertInstanceOf('Zend\GData\Analytics\AccountEntry', $entry);
        }
    }
}
