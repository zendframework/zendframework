<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Crypt;

use Zend\Crypt\Utils;

/**
 * Outside the Internal Function tests, tests do not distinguish between hash and mhash
 * when available. All tests use Hashing algorithms both extensions implement.
 */

/**
 * @group      Zend_Crypt
 */
class UtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testCompareStringsBasic()
    {
        $this->assertTrue(Utils::compareStrings('test', 'test'));
        $this->assertFalse(Utils::compareStrings('test', 'Test'));
    }
}
