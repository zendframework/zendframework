<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData;

use Zend\GData\GApps;

/**
 * @category   Zend
 * @package    Zend_GData_GApps
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_GApps
 */
class GAppsTest extends \PHPUnit_Framework_TestCase
{
    const TEST_DOMAIN = 'nowhere.invalid';

    public function setUp()
    {
        // These tests shouldn't be doing anything online, so we can use
        // bogous auth credentials.
        $this->gdata = new GApps(null, self::TEST_DOMAIN);
    }

    public function testMagicFactoryProvidesQueriesWithDomains()
    {
        $userQ = $this->gdata->newUserQuery();
        $this->assertTrue($userQ instanceof GApps\UserQuery);
        $this->assertEquals(self::TEST_DOMAIN, $userQ->getDomain());
        $this->assertEquals(null, $userQ->getUsername());

        $userQ = $this->gdata->newUserQuery('foo');
        $this->assertTrue($userQ instanceof GApps\UserQuery);
        $this->assertEquals(self::TEST_DOMAIN, $userQ->getDomain());
        $this->assertEquals('foo', $userQ->getUsername());
    }

    public function testMagicFactoryLeavesNonQueriesAlone()
    {
        $login = $this->gdata->newLogin('blah');
        $this->assertTrue($login instanceof \Zend\GData\GApps\Extension\Login);
        $this->assertEquals('blah', $login->username);
    }

    public function testEmptyResponseExceptionRaisesException()
    {
      $e = new \Zend\GData\App\HttpException();
      $success = false;
      try {
        $this->gdata->throwServiceExceptionIfDetected($e);
      } catch (\Zend\GData\App\IOException $f) {
        $success = true;
      }
      $this->assertTrue($success, 'Zend_GData_App_IOException not thrown');
    }

}
