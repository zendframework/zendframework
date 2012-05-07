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
 * @package    Zend_Locale
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Locale\Data;

use Zend\Locale\Data\Cldr,
    Zend\Locale\Exception\InvalidArgumentException,
    Zend\Locale\Locale,
    Zend\Cache\StorageFactory as CacheFactory,
    Zend\Cache\Storage\Adapter\AdapterInterface as CacheAdapter,
    ZendTest\Locale\Data\CldrTest;

/**
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Locale
 */
class CldrCachedTest extends CldrTest {
    
    private $_cache = null;

    public function setUp()
    {
        $this->_cacheDir = sys_get_temp_dir() . '/zend_locale_cldr';
        $this->_removeRecursive($this->_cacheDir);
        mkdir($this->_cacheDir);

        $this->_cache = CacheFactory::factory(array(
            'adapter' => array(
                'name' => 'Filesystem',
                'options' => array(
                    'ttl'       => 1,
                    'cache_dir' => $this->_cacheDir,
                )
            ),
            'plugins' => array(
                array(
                    'name' => 'serializer',
                    'options' => array(
                        'serializer' => 'php_serialize',
                    ),
                ),
            ),
        ));

        Cldr::setCache($this->_cache);
    }


    public function tearDown()
    {
        $this->_cache->clear(CacheAdapter::MATCH_ALL);
        $this->_removeRecursive($this->_cacheDir);
    }    
    
    
    protected function _removeRecursive($dir)
    {
        if (file_exists($dir)) {
            $dirIt = new \DirectoryIterator($dir);
            foreach ($dirIt as $entry) {
                $fname = $entry->getFilename();
                if ($fname == '.' || $fname == '..') {
                    continue;
                }

                if ($entry->isFile()) {
                    unlink($entry->getPathname());
                } else {
                    $this->_removeRecursive($entry->getPathname());
                }
            }

            rmdir($dir);
        }
    }
}
