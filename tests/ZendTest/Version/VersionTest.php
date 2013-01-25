<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_VersionTest.php
 */

use Zend\Version\Version;

/**
 * @category   Zend
 * @package    Zend_Version
 * @subpackage UnitTests
 * @group      Zend_Version
 */
class Zend_VersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that version_compare() and its "proxy"
     * Zend_Version::compareVersion() work as expected.
     */
    public function testVersionCompare()
    {
        $expect = -1;
        for ($i=0; $i < 2; $i++) {
            for ($j=0; $j < 12; $j++) {
                for ($k=0; $k < 20; $k++) {
                    foreach (array('dev', 'pr', 'PR', 'alpha', 'a1', 'a2', 'beta', 'b1', 'b2', 'RC', 'RC1', 'RC2', 'RC3', '', 'pl1', 'PL1') as $rel) {
                        $ver = "$i.$j.$k$rel";
                        $normalizedVersion = strtolower(Version::VERSION);
                        if (strtolower($ver) === $normalizedVersion
                            || strtolower("$i.$j.$k-$rel") === $normalizedVersion
                            || strtolower("$i.$j.$k.$rel") === $normalizedVersion
                            || strtolower("$i.$j.$k $rel") === $normalizedVersion
                        ) {
                            if ($expect == -1) {
                                $expect = 1;
                            }
                        } else {
                            $this->assertSame(
                                Version::compareVersion($ver),
                                $expect,
                                "For version '$ver' and Zend_Version::VERSION = '"
                                . Version::VERSION . "': result=" . (Version::compareVersion($ver))
                                . ', but expected ' . $expect);
                        }
                    }
                }
            }
        };
    }

    /**
     * @group ZF-10363
     */
    public function testFetchLatestVersion()
    {
        if (!constant('TESTS_ZEND_VERSION_ONLINE_ENABLED')) {
            $this->markTestSkipped('Version online tests are not enabled');
        }
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('This test requires openssl extension to be enabled in PHP');
        }
        $actual = Version::getLatest();

        $this->assertRegExp('/^[1-2](\.[0-9]+){2}/', $actual);
    }

    public function testFetchLatestZENDVersion()
    {
        if (!constant('TESTS_ZEND_VERSION_ONLINE_ENABLED')) {
            $this->markTestSkipped('Version online tests are not enabled');
        }
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('This test requires openssl extension to be enabled in PHP');
        }
        $actual = Version::getLatest('ZEND');

        $this->assertRegExp('/^[1-2](\.[0-9]+){2}/', $actual);
    }
}
