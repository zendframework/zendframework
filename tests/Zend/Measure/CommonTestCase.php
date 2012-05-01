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
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Measure;
use Zend\Cache\StorageFactory as CacheFactory,
    Zend\Cache\Storage\Adapter\AdapterInterface as CacheAdapter,
    Zend\Locale\Locale;

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Measure
 */
abstract class CommonTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * The used cache adapter
     * @var CacheAdapter
     */
    protected $cache;

    public function setUp()
    {
        $this->cache = CacheFactory::adapterFactory('memory', array('memory_limit' => 0));
        Locale::setCache($this->cache);
    }

    public function tearDown()
    {
        if ($this->cache) {
            $this->cache->clear(CacheAdapter::MATCH_ALL);
        }
    }
}
