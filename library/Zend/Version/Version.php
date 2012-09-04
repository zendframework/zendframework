<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Version.php
 */

namespace Zend\Version;

use Zend\Json\Json;

/**
 * Class to store and retrieve the version of Zend Framework.
 *
 * @category   Zend
 * @package    Zend_Version
 */
final class Version
{
    /**
     * Zend Framework version identification - see compareVersion()
     */
    const VERSION = '2.0.0';

    /**
     * The latest stable version Zend Framework available
     *
     * @var string
     */
    protected static $latestVersion;

    /**
     * Compare the specified Zend Framework version string $version
     * with the current Zend_Version::VERSION of Zend Framework.
     *
     * @param  string  $version  A version string (e.g. "0.7.1").
     * @return int           -1 if the $version is older,
     *                           0 if they are the same,
     *                           and +1 if $version is newer.
     *
     */
    public static function compareVersion($version)
    {
        $version = strtolower($version);
        $version = preg_replace('/(\d)pr(\d?)/', '$1a$2', $version);
        return version_compare($version, strtolower(self::VERSION));
    }

    /**
     * Fetches the version of the latest stable release.
     *
     * This uses the GitHub API (v3) and only returns refs that begin with
     * 'tags/release-'. Because GitHub returns the refs in alphabetical order,
     * we need to reduce the array to a single value, comparing the version
     * numbers with version_compare().
     *
     * @see http://developer.github.com/v3/git/refs/#get-all-references
     * @link https://api.github.com/repos/zendframework/zf2/git/refs/tags/release-
     * @return string
     */
    public static function getLatest()
    {
        if (null === self::$latestVersion) {
            self::$latestVersion = 'not available';
            $url  = 'https://api.github.com/repos/zendframework/zf2/git/refs/tags/release-';

            $apiResponse = Json::decode(file_get_contents($url), Json::TYPE_ARRAY);

            // Simplify the API response into a simple array of version numbers
            $tags = array_map(function($tag) {
                return substr($tag['ref'], 18); // Reliable because we're filtering on 'refs/tags/release-'
            }, $apiResponse);

            // Fetch the latest version number from the array
            self::$latestVersion = array_reduce($tags, function($a, $b) {
                return version_compare($a, $b, '>') ? $a : $b;
            });
        }

        return self::$latestVersion;
    }

    /**
     * Returns true if the running version of Zend Framework is
     * the latest (or newer??) than the latest tag on GitHub,
     * which is returned by static::getLatest().
     *
     * @return boolean
     */
    public static function isLatest()
    {
        return static::compareVersion(static::getLatest()) < 1;
    }
}
