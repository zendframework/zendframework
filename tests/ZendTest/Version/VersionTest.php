<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Version;

use Zend\Http;
use Zend\Version\Version;

/**
 * @group      Zend_Version
 */
class VersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that version_compare() and its "proxy"
     * Zend\Version\Version::compareVersion() work as expected.
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
                                "For version '$ver' and Zend\Version\Version::VERSION = '"
                                . Version::VERSION . "': result=" . (Version::compareVersion($ver))
                                . ', but expected ' . $expect);
                        }
                    }
                }
            }
        };
    }

    /**
     * Run in separate process to avoid Version::$latestParameter caching
     *
     * @group ZF-10363
     * @runInSeparateProcess
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

    /**
     * Run in separate process to avoid Version::$latestParameter caching
     *
     * @runInSeparateProcess
     */
    public function testFetchLatestGithubVersion()
    {
        if (!constant('TESTS_ZEND_VERSION_ONLINE_ENABLED')) {
            $this->markTestSkipped('Version online tests are not enabled');
        }
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('This test requires openssl extension to be enabled in PHP');
        }

        $actual = Version::getLatest(Version::VERSION_SERVICE_GITHUB);

        $this->assertRegExp('/^[1-2](\.[0-9]+){2}/', $actual);
    }

    /**
     * Run in separate process to avoid Version::$latestParameter caching
     *
     * @runInSeparateProcess
     */
    public function testFetchLatestVersionWarnsIfAllowUrlFopenIsDisabled()
    {
        if (!constant('TESTS_ZEND_VERSION_ONLINE_ENABLED')) {
            $this->markTestSkipped('Version online tests are not enabled');
        }
        if (ini_get('allow_url_fopen')) {
            $this->markTestSkipped('Test only works with allow_url_fopen disabled');
        }

        $this->setExpectedException('PHPUnit_Framework_Error_Warning');

        Version::getLatest(Version::VERSION_SERVICE_ZEND);
    }

    /**
     * Run in separate process to avoid Version::$latestParameter caching
     *
     * @runInSeparateProcess
     */
    public function testFetchLatestVersionWarnsIfBadServiceIsPassed()
    {
        if (!constant('TESTS_ZEND_VERSION_ONLINE_ENABLED')) {
            $this->markTestSkipped('Version online tests are not enabled');
        }

        $this->setExpectedException('PHPUnit_Framework_Error_Warning');

        Version::getLatest('bogus service');
    }

    /**
     * Run in separate process to avoid Version::$latestParameter caching
     *
     * @runInSeparateProcess
     */
    public function testFetchLatestVersionUsesSuppliedZendHttpClient()
    {
        if (!constant('TESTS_ZEND_VERSION_ONLINE_ENABLED')) {
            $this->markTestSkipped('Version online tests are not enabled');
        }
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('This test requires openssl extension to be enabled in PHP');
        }

        $httpClient = new Http\Client(
            'http://example.com',
            array(
                'sslverifypeer' => false,
            )
        );

        $actual = Version::getLatest(Version::VERSION_SERVICE_GITHUB, $httpClient);
        $this->assertRegExp('/^[1-2](\.[0-9]+){2}/', $actual);

        $lastRequest = $httpClient->getRequest();
        $this->assertContains('github.com', (string) $lastRequest->getUri());
    }

    /**
     * Run in separate process to avoid Version::$latestParameter caching
     *
     * @runInSeparateProcess
     */
    public function testFetchLatestVersionDoesNotThrowZendHttpClientException()
    {
        if (!constant('TESTS_ZEND_VERSION_ONLINE_ENABLED')) {
            $this->markTestSkipped('Version online tests are not enabled');
        }
        if (!extension_loaded('openssl')) {
            $this->markTestSkipped('This test requires openssl extension to be enabled in PHP');
        }

        $httpClient = new Http\Client(
            'http://example.com',
            array(
                'sslcapath' => '/dev/null',
                'sslverifypeer' => true,
            )
        );

        $actual = Version::getLatest(Version::VERSION_SERVICE_GITHUB, $httpClient);
        $this->assertEquals('not available', $actual);
    }
}
