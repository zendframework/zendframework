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
 * @package    Zend_Translator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Translator\Adapter;

use Zend\Cache\StorageFactory as CacheFactory,
    Zend\Cache\Storage\Adapter as CacheAdapter,
    Zend\Locale,
    Zend\Translator,
    Zend\Translator\Adapter;

/**
 * @category   Zend
 * @package    Zend_Translator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Translator
 */
class ArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Error flag
     *
     * @var boolean
     */
    protected $_errorOccurred = false;

    public function setUp()
    {
        $this->_cacheDir = sys_get_temp_dir() . '/zend_translator_array';
        $this->_removeRecursive($this->_cacheDir);
        mkdir($this->_cacheDir);

        if (Adapter\ArrayAdapter::hasCache()) {
            Adapter\ArrayAdapter::clearCache();
            Adapter\ArrayAdapter::removeCache();
        }
    }

    public function tearDown()
    {
        if (Adapter\ArrayAdapter::hasCache()) {
            Adapter\ArrayAdapter::clearCache();
            Adapter\ArrayAdapter::removeCache();
        }
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

    public function testCreate()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter = new Adapter\ArrayAdapter(array());
        restore_error_handler();
        $this->assertTrue($adapter instanceof Adapter\ArrayAdapter);
    }

    public function testCreate2()
    {
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter = new Adapter\ArrayAdapter('hastofail', 'en');
    }

    public function testCreate3()
    {
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/failed.php', 'en');
    }

    public function testToString()
    {
        $adapter = new Adapter\ArrayAdapter(array('msg1' => 'Message 1 (en)', 'msg2' => 'Message 2 (en)', 'msg3' => 'Message 3 (en)'), 'de');
        $this->assertEquals('ArrayAdapter', $adapter->toString());
    }

    public function testTranslate()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 1 (en)', $adapter->_('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $this->assertEquals('Küchen Möbel (en)', $adapter->translate('Cooking furniture'));
        $this->assertEquals('Cooking furniture (en)', $adapter->translate('Küchen Möbel'));
    }

    public function testIsTranslated()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertTrue($adapter->isTranslated('Message 1'));
        $this->assertFalse($adapter->isTranslated('Message 6'));
        $this->assertTrue($adapter->isTranslated('Message 1', true));
        $this->assertFalse($adapter->isTranslated('Message 1', true, 'en_US'));
        $this->assertTrue($adapter->isTranslated('Message 1', false, 'en_US'));
        $this->assertFalse($adapter->isTranslated('Message 1', false, 'es'));
        $this->assertFalse($adapter->isTranslated('Message 1', 'es'));
        $this->assertFalse($adapter->isTranslated('Message 1', 'xx_XX'));
        $this->assertTrue($adapter->isTranslated('Message 1', 'en_XX'));
    }

    public function testLoadTranslationData()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $adapter->translate('Message 4'));
        $this->assertEquals('Message 2', $adapter->translate('Message 2', 'ru'));
        $this->assertEquals('Message 1', $adapter->translate('Message 1', 'xx'));
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1', 'en_US'));

        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $adapter->addTranslation(__DIR__ . '/_files/translation_en.php', 'xx');
    }

    public function testLoadTranslationData2()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        $adapter->addTranslation(__DIR__ . '/_files/translation_en2.php', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptions()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        $adapter->setOptions(array('testoption' => 'testkey'));
        $expected = array(
            'testoption'      => 'testkey',
            'clear'           => false,
            'content'         => __DIR__ . '/_files/translation_en.php',
            'scan'            => null,
            'locale'          => 'en',
            'ignore'          => '.',
            'disableNotices'  => false,
            'log'             => false,
            'logMessage'      => 'Untranslated message within \'%locale%\': %message%',
            'logUntranslated' => false,
            'reload'          => false,
        );

        $options = $adapter->getOptions();

        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $options);
            $this->assertEquals($value, $options[$key]);
        }

        $this->assertEquals('testkey', $adapter->getOptions('testoption'));
        $this->assertTrue(is_null($adapter->getOptions('nooption')));
    }

    public function testClearing()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals('Message 1 (en)', $adapter->translate('Message 1'));
        $this->assertEquals('Message 6', $adapter->translate('Message 6'));
        $adapter->addTranslation(__DIR__ . '/_files/translation_en2.php', 'de', array('clear' => true));
        $this->assertEquals('Nachricht 1', $adapter->translate('Message 1'));
        $this->assertEquals('Message 4', $adapter->translate('Message 4'));
    }

    public function testLocale()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals('en', $adapter->getLocale());
        $locale = new Locale\Locale('en');
        $adapter->setLocale($locale);
        $this->assertEquals('en', $adapter->getLocale());
    }

    public function testLocale2()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        $this->setExpectedException('Zend\Translator\Exception');
        $adapter->setLocale('nolocale');
    }

    public function testLocale3()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter->setLocale('de');
        restore_error_handler();
        $this->assertEquals('de', $adapter->getLocale());
    }

    public function testList()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals(array('en' => 'en'), $adapter->getList());
        $adapter->addTranslation(array('msg1'), 'de');
        $this->assertEquals(array('en' => 'en', 'de' => 'de'), $adapter->getList());
        $this->assertTrue($adapter->isAvailable('de'));
        $locale = new Locale\Locale('en');
        $this->assertTrue($adapter->isAvailable($locale));
        $this->assertFalse($adapter->isAvailable('sr'));
    }

    public function testOptionLocaleDirectory()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/testarray', 'de_AT', array('scan' => Translator\Translator::LOCALE_DIRECTORY));
        $this->assertEquals(array('de_AT' => 'de_AT', 'en_GB' => 'en_GB', 'ja' => 'ja'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testOptionLocaleFilename()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/testarray', 'de_DE', array('scan' => Translator\Translator::LOCALE_FILENAME));
        $this->assertEquals(array('de_DE' => 'de_DE', 'en_US' => 'en_US', 'ja' => 'ja'), $adapter->getList());
        $this->assertEquals('Nachricht 8', $adapter->translate('Message 8'));
    }

    public function testLoadArrayFile()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php');
        $this->assertTrue($adapter instanceof Adapter\ArrayAdapter);
    }

    public function testDisablingNotices()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter = new Adapter\ArrayAdapter(array(), 'en');
        $this->assertTrue($this->_errorOccurred);
        restore_error_handler();
        $this->_errorOccurred = false;
        $this->assertTrue($adapter instanceof Adapter\ArrayAdapter);

        set_error_handler(array($this, 'errorHandlerIgnore'));
        $adapter = new Adapter\ArrayAdapter(array(), 'en', array('disableNotices' => true));
        $this->assertFalse($this->_errorOccurred);
        restore_error_handler();
        $this->assertTrue($adapter instanceof Adapter\ArrayAdapter);
    }

    public function testGettingAllMessageIds()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals(6, count($adapter->getMessageIds()));
        $test = $adapter->getMessageIds();
        $this->assertEquals('Message 1', $test[0]);
    }

    public function testGettingMessages()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals(6, count($adapter->getMessages()));
        $test = $adapter->getMessages();
        $this->assertEquals('Message 1 (en)', $test['Message 1']);
    }

    public function testGettingAllMessages()
    {
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        $this->assertEquals(1, count($adapter->getMessages('all')));
        $test = $adapter->getMessages('all');
        $this->assertEquals('Message 1 (en)', $test['en']['Message 1']);
    }

    public function testCaching()
    {
        $cache = CacheFactory::factory(array(
            'adapter' => array(
                'name' => 'Filesystem',
                'options' => array(
                    'ttl'       => 120,
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

        $this->assertFalse(Adapter\ArrayAdapter::hasCache());
        Adapter\ArrayAdapter::setCache($cache);
        $this->assertTrue(Adapter\ArrayAdapter::hasCache());

        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        $cache   = Adapter\ArrayAdapter::getCache();
        $this->assertTrue($cache instanceof CacheAdapter);
        unset ($adapter);

        Adapter\ArrayAdapter::setCache($cache);
        $this->assertTrue(Adapter\ArrayAdapter::hasCache());
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        $cache   = Adapter\ArrayAdapter::getCache();
        $this->assertTrue($cache instanceof CacheAdapter);

        Adapter\ArrayAdapter::removeCache();
        $this->assertFalse(Adapter\ArrayAdapter::hasCache());

        $cache->setItem('testid', 'testdata');
        Adapter\ArrayAdapter::setCache($cache);
        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        Adapter\ArrayAdapter::removeCache();
        $temp = $cache->getItem('testid');
        $this->assertEquals('testdata', $temp);
    }

    public function testLoadingFilesIntoCacheAfterwards()
    {
        $cache = CacheFactory::factory(array(
            'adapter' => array(
                'name' => 'Filesystem',
                'options' => array(
                    'ttl'       => 120,
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

        $this->assertFalse(Adapter\ArrayAdapter::hasCache());
        Adapter\ArrayAdapter::setCache($cache);
        $this->assertTrue(Adapter\ArrayAdapter::hasCache());

        $adapter = new Adapter\ArrayAdapter(__DIR__ . '/_files/translation_en.php', 'en');
        $cache   = Adapter\ArrayAdapter::getCache();
        $this->assertTrue($cache instanceof CacheAdapter);

        $adapter->addTranslation(__DIR__ . '/_files/translation_en.php', 'ru', array('reload' => true));
        $test = $adapter->getMessages('all');
        $this->assertEquals(6, count($test['ru']));
    }

    /**
     * Ignores a raised PHP error when in effect, but throws a flag to indicate an error occurred
     *
     * @param  integer $errno
     * @param  string  $errstr
     * @param  string  $errfile
     * @param  integer $errline
     * @param  array   $errcontext
     * @return void
     */
    public function errorHandlerIgnore($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $this->_errorOccurred = true;
    }
}
