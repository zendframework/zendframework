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
namespace ZendTest\Translator;

use Zend\Cache\StorageFactory as CacheFactory,
    Zend\Cache\Storage\Adapter as CacheAdapter,
    Zend\Locale,
    Zend\Log,
    Zend\Log\Writer,
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
class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_cacheDir = sys_get_temp_dir() . '/zend_translator';
        $this->_removeRecursive($this->_cacheDir);
        mkdir($this->_cacheDir);

        putenv("HTTP_ACCEPT_LANGUAGE=,ja,de-AT-DE;q=1,en_US;q=0.5");
        if (Translator\Translator::hasCache()) {
            Translator\Translator::clearCache();
            Translator\Translator::removeCache();
        }

        if (Adapter\ArrayAdapter::hasCache()) {
            Adapter\ArrayAdapter::clearCache();
            Adapter\ArrayAdapter::removeCache();
        }

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

        Translator\Translator::setCache($cache);
    }

    public function tearDown()
    {
        if (Translator\Translator::hasCache()) {
            Translator\Translator::clearCache();
            Translator\Translator::removeCache();
        }

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
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('1' => '1'));
        $this->assertTrue($lang instanceof Translator\Translator);
    }

    public function testLocaleInitialization()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'message1'), 'en');
        $this->assertEquals('en', $lang->getLocale());
    }

    public function testDefaultLocale()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'message1'));
        $defaultLocale = new Locale\Locale();
        $this->assertEquals($defaultLocale->toString(), $lang->getLocale());
    }

    public function testGetAdapter()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY , array('1' => '1'), 'en');
        $this->assertTrue($lang->getAdapter() instanceof Adapter\ArrayAdapter);

        $lang = new Translator\Translator(Translator\Translator::AN_GETTEXT , __DIR__ . '/Adapter/_files/translation_en.mo', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Adapter\Gettext);

        $lang = new Translator\Translator(Translator\Translator::AN_TMX , __DIR__ . '/Adapter/_files/translation_en.tmx', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Adapter\Tmx);

        $lang = new Translator\Translator(Translator\Translator::AN_CSV , __DIR__ . '/Adapter/_files/translation_en.csv', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Adapter\Csv);

        $lang = new Translator\Translator(Translator\Translator::AN_XLIFF , __DIR__ . '/Adapter/_files/translation_en.xliff', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Adapter\Xliff);

        $lang = new Translator\Translator('Qt' , __DIR__ . '/Adapter/_files/translation_en2.ts', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Adapter\Qt);

        $lang = new Translator\Translator('XmlTm' , __DIR__ . '/Adapter/_files/translation_en.xmltm', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Adapter\XmlTm);

        $lang = new Translator\Translator('Tbx' , __DIR__ . '/Adapter/_files/translation_en.tbx', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Adapter\Tbx);
    }

    public function testSetAdapter()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_GETTEXT , __DIR__ . '/Adapter/_files/translation_en.mo', 'en');
        $lang->setAdapter(Translator\Translator::AN_ARRAY, array('de' => 'de'));
        $this->assertTrue($lang->getAdapter() instanceof Adapter\ArrayAdapter);
    }

    public function testSetAdapter2()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_GETTEXT , __DIR__ . '/Adapter/_files/translation_en.mo', 'en');
        $this->setExpectedException('Zend\Translator\Exception\BadMethodCallException');
        $lang->xxxFunction();
    }

    public function testAddTranslation()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1'), 'en');

        $this->assertEquals('msg2', $lang->_('msg2'));

        $lang->addTranslation(array('msg2' => 'Message 2'), 'en');
        $this->assertEquals('Message 2', $lang->_('msg2'));
        $this->assertEquals('msg3',      $lang->_('msg3'));

        $lang->addTranslation(array('msg3' => 'Message 3'), 'en', array('clear' => true));
        $this->assertEquals('msg2',      $lang->_('msg2'));
        $this->assertEquals('Message 3', $lang->_('msg3'));
    }

    public function testGetLocale()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $this->assertEquals('en', $lang->getLocale());
    }

    public function testSetLocale()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertEquals('ru', $lang->getLocale());

        $lang->setLocale('en');
        $this->assertEquals('en', $lang->getLocale());

        $lang->setLocale('ru');
        $this->assertEquals('ru', $lang->getLocale());

        $lang->setLocale('ru_RU');
        $this->assertEquals('ru', $lang->getLocale());
    }

    public function testSetLanguage()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertEquals('ru', $lang->getLocale());

        $lang->setLocale('en');
        $this->assertEquals('en', $lang->getLocale());
    }

    public function testGetLanguageList()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertEquals(2, count($lang->getList()));
        $this->assertTrue(in_array('en', $lang->getList()));
        $this->assertTrue(in_array('ru', $lang->getList()));
    }

    public function testIsAvailable()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertTrue( $lang->isAvailable('en'));
        $this->assertTrue( $lang->isAvailable('ru'));
        $this->assertFalse($lang->isAvailable('fr'));
    }

    public function testTranslate()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1 (en)'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertEquals('Message 1 (en)', $lang->_('msg1', 'en'        ));
        $this->assertEquals('Message 1 (ru)', $lang->_('msg1'              ));
        $this->assertEquals('msg2',           $lang->_('msg2', 'en'        ));
        $this->assertEquals('msg2',           $lang->_('msg2'              ));
        $this->assertEquals('Message 1 (en)', $lang->translate('msg1', 'en'));
        $this->assertEquals('Message 1 (ru)', $lang->translate('msg1'      ));
        $this->assertEquals('msg2',           $lang->translate('msg2', 'en'));
        $this->assertEquals('msg2',           $lang->translate('msg2'      ));
    }

    public function testIsTranslated()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1 (en)'), 'en_US');
        $this->assertTrue( $lang->isTranslated('msg1'             ));
        $this->assertFalse($lang->isTranslated('msg2'             ));
        $this->assertFalse($lang->isTranslated('msg1', false, 'en'));
        $this->assertFalse($lang->isTranslated('msg1', true,  'en'));
        $this->assertFalse($lang->isTranslated('msg1', false, 'ru'));
    }

    public function testWithOption()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_CSV , __DIR__ . '/Adapter/_files/translation_otherdelimiter.csv', 'en', array('delimiter' => ','));
        $this->assertEquals('Message 1 (en)', $lang->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $lang->translate('Message 4,'));
        $this->assertEquals('Message 5, (en)', $lang->translate('Message 5'));
    }

    public function testDirectorySearch()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_CSV, __DIR__ . '/Adapter/_files/testcsv', 'de_AT', array('scan' => Translator\Translator::LOCALE_DIRECTORY));
        $this->assertEquals(array('de_AT' => 'de_AT', 'en_GB' => 'en_GB'), $lang->getList());
        $this->assertEquals('Nachricht 8', $lang->translate('Message 8'));
    }

    public function testFileSearch()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_CSV, __DIR__ . '/Adapter/_files/testcsv', 'de_DE', array('scan' => Translator\Translator::LOCALE_FILENAME));
        $this->assertEquals(array('de_DE' => 'de_DE', 'en_US' => 'en_US'), $lang->getList());
        $this->assertEquals('Nachricht 8', $lang->translate('Message 8'));
    }

    public function testTestingCacheHandling()
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

        Translator\Translator::setCache($cache);

        $cache = Translator\Translator::getCache();
        $this->assertTrue($cache instanceof CacheAdapter);
        $this->assertTrue(Translator\Translator::hasCache());

        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1 (en)'), 'en');
        $adapter = $lang->getAdapter();
        $this->assertTrue($adapter instanceof Adapter\ArrayAdapter);
        $adaptercache = $adapter->getCache();
        $this->assertTrue($adaptercache instanceof CacheAdapter);

        Translator\Translator::clearCache();
        $this->assertTrue(Translator\Translator::hasCache());
        Translator\Translator::removeCache();
        $this->assertFalse(Translator\Translator::hasCache());
    }

    public function testExceptionWhenNoAdapterClassWasSet()
    {
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $lang = new Translator\Translator('Zend\Locale', __DIR__ . '/../_files/test2', null, array('scan' => Translator\Translator::LOCALE_FILENAME));
    }

    public function testZF3679()
    {
        $locale = new Locale\Locale('de_AT');
        \Zend\Registry::set('Zend_Locale', $locale);

        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'message1'), 'de_AT');
        $this->assertEquals('de_AT', $lang->getLocale());
        \Zend\Registry::_unsetInstance();
    }

    /**
     * ZF-4994
     */
    public function testCamelCasedOptions()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_CSV, __DIR__ . '/Adapter/_files/translation_otherdelimiter.csv', 'en', array('delimiter' => ','));
        $lang->setOptions(array('myOption' => true));
        $this->assertTrue($lang->getOptions('myOption'));
    }

    /**
     * ZF-4905
     */
    public function testPathNameWithColonResolution()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_CSV, __DIR__ . '/Adapter/../Adapter/_files', 'en', array('delimiter' => ','));
        $this->assertEquals('en', $lang->getLocale());
    }

    public function testUntranslatedMessageWithTriggeredError()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_CSV, __DIR__ . '/Adapter/_files', 'en', array('delimiter' => ','));
        $this->assertEquals('ignored', $lang->translate('ignored'));

        $this->_errorOccured = false;
        $lang->setOptions(array('logUntranslated' => true));
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $this->assertEquals('ignored', $lang->translate('ignored'));
        $this->assertTrue($this->_errorOccured);
        restore_error_handler();
    }

    public function testLogUntranslatedMessage()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_CSV, __DIR__ . '/Adapter/_files', 'en', array('delimiter' => ','));
        $this->assertEquals('ignored', $lang->translate('ignored'));

        $stream = fopen('php://memory', 'w+');
        $writer = new Writer\Stream($stream);
        $log    = new Log\Logger($writer);

        $lang->setOptions(array('logUntranslated' => true, 'log' => $log));
        $this->assertEquals('ignored', $lang->translate('ignored'));

        rewind($stream);
        $this->assertContains('ignored', stream_get_contents($stream));
    }

    public function testSettingUnknownLocaleWithTriggeredError()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_CSV, __DIR__ . '/Adapter/_files', 'en', array('delimiter' => ','));
        $this->_errorOccured = false;
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $lang->setLocale('ru');
        $this->assertEquals('ru', $lang->getLocale('ru'));
        $this->assertTrue($this->_errorOccured);
        restore_error_handler();
    }

    public function testSettingUnknownLocaleWritingToLog()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_CSV, __DIR__ . '/Adapter/_files', 'en', array('delimiter' => ','));

        $stream = fopen('php://memory', 'w+');
        $writer = new Writer\Stream($stream);
        $log    = new Log\Logger($writer);

        $lang->setOptions(array('log' => $log));
        $lang->setLocale('ru');

        rewind($stream);
        $this->assertContains('has to be added', stream_get_contents($stream));
    }

    public function testSettingNoLogAsLog()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_CSV, __DIR__ . '/Adapter/_files', 'en', array('delimiter' => ','));
        $this->setExpectedException('Zend\Translator\Exception\InvalidArgumentException');
        $lang->setOptions(array('log' => 'nolog'));
    }

    public function testSettingUnknownLocaleWritingToSelfDefinedLog()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_CSV, __DIR__ . '/Adapter/_files', 'en', array('delimiter' => ','));
        $this->assertEquals('ignored', $lang->translate('ignored'));

        $stream = fopen('php://memory', 'w+');
        $writer = new Writer\Stream($stream);
        $log    = new Log\Logger($writer);

        $lang->setOptions(array('logUntranslated' => true, 'log' => $log, 'logMessage' => 'Self defined log message'));
        $this->assertEquals('ignored', $lang->translate('ignored'));

        rewind($stream);
        $this->assertContains('Self defined log message', stream_get_contents($stream));
    }

    /**
     * Tests if cached options are read from the cache for a new instance
     */
    public function testGetOptionsFromCache()
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

        Translator\Translator::setCache($cache);

        $lang = new Translator\Translator(Translator\Translator::AN_CSV, __DIR__ . '/Adapter/_files', 'en', array('delimiter' => ','));
        $lang->setOptions(array('logMessage' => 'test'));
        $this->assertEquals('test', $lang->getOptions('logMessage'));
        unset($lang);

        $lang2 = new Translator\Translator(Translator\Translator::AN_CSV, __DIR__ . '/Adapter/_files', 'en', array('delimiter' => ','));
        $this->assertEquals('test', $lang2->getOptions('logMessage'));
    }

    /**
     * Tests if setting locale as options sets locale
     */
    public function testSetLocaleAsOption()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $lang->setOptions(array('locale' => 'ru'));
        $this->assertEquals('ru', $lang->getLocale());
        $lang->setOptions(array('locale' => 'en'));
        $this->assertEquals('en', $lang->getLocale());
    }

    /**
     * Tests getting null returns all options
     */
    public function testGettingAllOptions()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $this->assertTrue(is_array($lang->getOptions()));
    }

    /**
     * Tests if setting locale as options sets locale
     */
    public function testGettingUnknownOption()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $this->assertEquals(null, $lang->getOptions('unknown'));
    }

    /**
     * Tests getting of all message ids works
     */
    public function testGettingAllMessageIds()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1', 'msg2' => 'Message 2'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertEquals(array('msg1'), $lang->getMessageIds());
        $this->assertEquals(array('msg1', 'msg2'), $lang->getMessageIds('en'));
    }

    /**
     * Tests getting of single message ids
     */
    public function testGettingSingleMessageIds()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1', 'msg2' => 'Message 2'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertEquals('msg1', $lang->getMessageId('Message 1 (ru)'));
        $this->assertEquals('msg2', $lang->getMessageId('Message 2', 'en'));
        $this->assertFalse($lang->getMessageId('Message 5'));
    }

    /**
     * Tests getting of all messages
     */
    public function testGettingAllMessages()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1', 'msg2' => 'Message 2'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertEquals(array('msg1' => 'Message 1 (ru)'), $lang->getMessages());
        $this->assertEquals(
            array('msg1' => 'Message 1', 'msg2' => 'Message 2'),
            $lang->getMessages('en'));
        $this->assertEquals(
            array(
                'en' => array('msg1' => 'Message 1', 'msg2' => 'Message 2'),
                'ru' => array('msg1' => 'Message 1 (ru)')),
            $lang->getMessages('all'));
    }

    /**
     * Tests getting default plurals
     */
    public function testGettingPlurals()
    {
        $lang = new Translator\Translator(
            Translator\Translator::AN_ARRAY,
            array('singular' =>
                array('plural_0 (en)',
                    'plural_1 (en)',
                    'plural_2 (en)',
                    'plural_3 (en)'),
                'plural' => ''), 'en'
        );

        $this->assertEquals('plural_0 (en)', $lang->translate(array('singular', 'plural', 1)));
        $this->assertEquals('plural_1 (en)', $lang->translate(array('singular', 'plural', 2)));

        $this->assertEquals('plural_0 (en)', $lang->plural('singular', 'plural', 1));
        $this->assertEquals('plural_1 (en)', $lang->plural('singular', 'plural', 2));
    }

    /**
     * Tests getting plurals from lowered locale
     */
    public function testGettingPluralsFromLoweredLocale()
    {
        $lang = new Translator\Translator(
            Translator\Translator::AN_ARRAY,
            array('singular' =>
                array('plural_0 (en)',
                    'plural_1 (en)',
                    'plural_2 (en)',
                    'plural_3 (en)'),
                'plural' => ''), 'en'
        );
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'en_US');
        $lang->setLocale('en_US');

        $this->assertEquals('plural_0 (en)', $lang->translate(array('singular', 'plural', 1)));
        $this->assertEquals('plural_0 (en)', $lang->plural('singular', 'plural', 1));
    }

    /**
     * Tests getting plurals from unknown locale
     */
    public function testGettingPluralsFromUnknownLocale()
    {
        $lang = new Translator\Translator(
            Translator\Translator::AN_ARRAY,
            array('singular' =>
                array('plural_0 (en)',
                    'plural_1 (en)',
                    'plural_2 (en)',
                    'plural_3 (en)'),
                'plural' => ''), 'en'
        );

        $this->assertEquals('singular', $lang->translate(array('singular', 'plural', 1), 'ru'));
        $this->assertEquals('singular', $lang->plural('singular', 'plural', 1, 'ru'));
        $this->assertEquals('plural', $lang->translate(array('singular', 'plural', 'plural2', 2, 'en'), 'ru'));
        $this->assertEquals('plural', $lang->plural('singular', 'plural', 2, 'ru'));
    }

    public function testPluralsWithGettext()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_GETTEXT , __DIR__ . '/Adapter/_files/translation_en.mo', 'en');

        $this->assertEquals('Message 5 (en) Plural 0', $lang->translate(array('Message 5', 'Message 5 Plural', 1)));
        $this->assertEquals('Message 5 (en) Plural 0', $lang->plural('Message 5', 'Message 5 Plural', 1));
        $this->assertEquals('Message 5 (en) Plural 1', $lang->translate(array('Message 5', 'Message 5 Plural', 2)));
        $this->assertEquals('Message 5 (en) Plural 1', $lang->plural('Message 5', 'Message 5 Plural', 2));
    }

    public function testPluralsWithCsv()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_CSV , __DIR__ . '/Adapter/_files/translation_en.csv', 'en');

        $this->assertEquals('Message 6 (en) Plural 0', $lang->translate(array('Message 6', 'Message 6 Plural1', 1)));
        $this->assertEquals('Message 6 (en) Plural 0', $lang->plural('Message 6', 'Message 6 Plural1', 1));
        $this->assertEquals('Message 6 (en) Plural 1', $lang->translate(array('Message 6', 'Message 6 Plural1', 2)));
        $this->assertEquals('Message 6 (en) Plural 1', $lang->plural('Message 6', 'Message 6 Plural1', 2));
    }

    /**
     * ZF-6671
     */
    public function testAddTranslationAfterwards()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $this->assertEquals('Message 1', $lang->_('msg1'));

        $lang->addTranslation(array('msg1' => 'Message 1 (en)'), 'en');
        $this->assertEquals('Message 1 (en)', $lang->_('msg1'));
    }

    /**
     * ZF-7560
     */
    public function testUseNumericTranslations()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, array(0 => 'Message 1', 2 => 'Message 2'), 'en');
        $this->assertEquals('Message 1', $lang->_(0));
        $this->assertEquals('Message 2', $lang->_(2));

        $lang->addTranslation(array(4 => 'Message 4'), 'en');
        $this->assertEquals('Message 4', $lang->_(4));
    }

    /**
     * ZF-7508
     */
    public function testDontLogUntranslatedMessageWithIsTranslated()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_CSV, __DIR__ . '/Adapter/_files', 'en', array('delimiter' => ','));
        $this->assertFalse($lang->isTranslated('ignored'));

        $stream = fopen('php://memory', 'w+');
        $writer = new Writer\Stream($stream);
        $log    = new Log\Logger($writer);

        $lang->setOptions(array('logUntranslated' => true, 'log' => $log));
        $this->assertFalse($lang->isTranslated('ignored'));

        rewind($stream);
        $this->assertNotContains('ignored', stream_get_contents($stream));
    }

    /**
     * ZF-7130
     */
    public function testMultiFolderScan()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, __DIR__ . '/Adapter/_files/testarray', 'en_GB', array('scan' => Translator\Translator::LOCALE_DIRECTORY));
        $this->assertEquals('Message 1 (ja)', $lang->_('Message 1', 'ja'        ));
        $this->assertEquals('Message 1 (en)', $lang->_('Message 1'              ));
    }

    /**
     * ZF-7214
     */
    public function testMultiClear()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, __DIR__ . '/Adapter/_files/testarray', 'en_GB', array('scan' => Translator\Translator::LOCALE_DIRECTORY));
        $this->assertEquals('Message 1 (ja)', $lang->_('Message 1', 'ja'));
        $lang->addTranslation(__DIR__ . '/Adapter/_files/translation_en.php', 'ja', array('clear'));
        $this->assertEquals('Message 1 (en)', $lang->_('Message 1', 'ja'));
    }

    /**
     * ZF-7941
     */
    public function testEmptyTranslation()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, null, null, array('disableNotices' => true));
        $this->assertEquals(0, count($lang->getList()));
    }

    /**
     * Translating Object
     */
    public function testObjectTranslation()
    {
        $lang = new Translator\Translator(Translator\Translator::AN_ARRAY, __DIR__ . '/Adapter/_files/testarray', 'en_GB', array('scan' => Translator\Translator::LOCALE_DIRECTORY));
        $this->assertEquals('Message 1 (ja)', $lang->_('Message 1', 'ja'));

        $this->assertEquals($lang, $lang->translate($lang));
    }

    /**
     * This test must be done BEFORE own rules are set to prevent unintentional behaviour
     *
     * @group ZF-11173
     */
    public function testRoutingPlurals()
    {
        $lang = new Translator\Translator(
            Translator\Translator::AN_ARRAY,
            array('singular' =>
                array('plural_0 (en)',
                    'plural_1 (en)',
                    'plural_2 (en)',
                    'plural_3 (en)'),
                'plural' => ''),
            'en',
            array(
                'route' => array('fr' => 'en'),
            )
        );

        $lang->addTranslation(array('msg1' => 'Message 1 (fr)'), 'fr');
        $this->assertEquals('plural_0 (en)', $lang->plural('singular', 'plural', 1));
        $this->assertEquals('plural_1 (en)', $lang->plural('singular', 'plural', 2));
        $lang->setLocale('fr');
        $this->assertEquals('plural_0 (en)', $lang->plural('singular', 'plural', 1));
        $this->assertEquals('plural_1 (en)', $lang->plural('singular', 'plural', 2));
    }

    /**
     * Tests getting plurals from own rule
     */
    public function testGettingPluralsUsingOwnRule()
    {
        $lang = new Translator\Translator(
            Translator\Translator::AN_ARRAY,
            array('singular' =>
                array('plural_0 (en)',
                    'plural_1 (en)',
                    'plural_2 (en)',
                    'plural_3 (en)'),
                'plural' => ''), 'en'
        );
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'en_US');
        $lang->setLocale('en_US');

        Translator\Plural::setPlural(array($this, 'customPlural'), 'en_US');
        $this->assertEquals('plural_1 (en)', $lang->translate(array('singular', 'plural', 1)));
        $this->assertEquals('plural_1 (en)', $lang->plural('singular', 'plural', 1));
        $this->assertEquals('plural_1 (en)', $lang->translate(array('singular', 'plural', 0)));
        $this->assertEquals('plural_1 (en)', $lang->plural('singular', 'plural', 0));
    }

    /**
     * @group ZF-9489
     */
    public function testAddingAdapterToSourcealsUsingOwnRule()
    {
        $translate = new Translator\Translator(
            Translator\Translator::AN_ARRAY,
            array('singular' =>
                array('plural_0 (en)',
                    'plural_1 (en)',
                    'plural_2 (en)',
                    'plural_3 (en)'),
                'plural' => ''), 'en'
        );

        $this->assertFalse($translate->isTranslated('Message 1'));
        $adapter = new Adapter\Gettext(__DIR__ . '/Adapter/_files/translation_en.mo', 'en');
        $translate->addTranslation($adapter);

        $this->assertTrue($adapter->isTranslated('Message 1'));

        $adapter2 = new Adapter\Gettext(__DIR__ . '/Adapter/_files/testmo/de_AT/LC_TEST/translation-de_DE.mo', 'de_AT');
        $adapter2->addTranslation(__DIR__ . '/Adapter/_files/translation_en2.mo', 'fr');
        $translate->addTranslation($adapter2, 'fr');

        $languages = $translate->getList();
        $this->assertFalse(array_key_exists('de_AT', $languages));
        $this->assertTrue(array_key_exists('fr', $languages));
    }

    /**
     * @group ZF-9500
     */
    public function testIgnoreMultipleDirectories()
    {
        $translate = new Translator\Translator(
            Translator\Translator::AN_ARRAY,
            __DIR__ . '/Adapter/_files/testarray/',
            'auto',
            array(
                'scan' => Translator\Translator::LOCALE_FILENAME,
                'ignore' => array('.', 'ignoreme', 'LC_TEST')
            )
        );

        $langs = $translate->getList();
        $this->assertFalse(array_key_exists('de_DE', $langs));
        $this->assertTrue(array_key_exists('ja', $langs));
        $this->assertTrue(array_key_exists('en_US', $langs));

        $translate2 = new Translator\Translator(
            Translator\Translator::AN_ARRAY,
            __DIR__ . '/Adapter/_files/testarray/',
            'en_US',
            array(
                'scan' => Translator\Translator::LOCALE_FILENAME,
                'ignore' => array('.', 'regex_1' => '/de_DE/', 'regex' => '/ja/'),
            )
        );

        $langs = $translate2->getList();
        $this->assertFalse(array_key_exists('de_DE', $langs));
        $this->assertFalse(array_key_exists('ja', $langs));
        $this->assertTrue(array_key_exists('en_US', $langs));
    }

    /**
     * @group ZF-2736
     */
    public function testReroutingForTranslations()
    {
        $translate = new Translator\Translator(
            array(
                'adapter' => Translator\Translator::AN_ARRAY,
                'content' => __DIR__ . '/Adapter/_files/testarray/',
                'locale'  => 'auto',
                'scan'    => Translator\Translator::LOCALE_FILENAME,
                'ignore'  => array('.', 'ignoreme', 'LC_OTHER'),
                'route'   => array('ja' => 'en_US'),
                'routeHttp' => false,
            )
        );

        $translate2 = new Translator\Translator(
            array(
                'adapter' => Translator\Translator::AN_CSV,
                'content' => __DIR__ . '/Adapter/_files/translation_en.csv',
                'locale'  => 'en_US',
                'routeHttp' => false,
            )
        );

        $translate->addTranslation($translate2);
        $langs = $translate->getList();
        $this->assertFalse(array_key_exists('de_AT', $langs));
        $this->assertTrue(array_key_exists('ja', $langs));
        $this->assertTrue(array_key_exists('en_US', $langs));
        $this->assertEquals('Message 5 (en)', $translate->translate('Message 5', 'ja'));
    }

    /**
     * @group ZF-2736
     */
    public function testCircleReroutingForTranslations()
    {
        $translate = new Translator\Translator(
            array(
                'adapter' => Translator\Translator::AN_ARRAY,
                'content' => __DIR__ . '/Adapter/_files/testarray/',
                'locale'  => 'auto',
                'scan'    => Translator\Translator::LOCALE_FILENAME,
                'ignore'  => array('.', 'ignoreme', 'LC_TEST'),
                'route'   => array('ja' => 'en_US', 'en_US' => 'ja'),
            )
        );

        $translate2 = new Translator\Translator(
            array(
                'adapter' => Translator\Translator::AN_CSV,
                'content' => __DIR__ . '/Adapter/_files/translation_en.csv',
                'locale'  => 'en_US',
            )
        );

        $translate->addTranslation($translate2);

        $langs = $translate->getList();
        $this->assertFalse(array_key_exists('de_DE', $langs));
        $this->assertTrue(array_key_exists('ja', $langs));
        $this->assertTrue(array_key_exists('en_US', $langs));
        $this->assertEquals('Message 5 (en)', $translate->translate('Message 5', 'ja'));
        $this->assertEquals('Message 10', $translate->translate('Message 10', 'ja'));
    }

    /**
     * @group ZF-2736
     */
    public function testDoubleReroutingForTranslations()
    {
        $translate = new Translator\Translator(
            array(
                'adapter' => Translator\Translator::AN_ARRAY,
                'content' => __DIR__ . '/Adapter/_files/testarray/',
                'locale'  => 'auto',
                'scan'    => Translator\Translator::LOCALE_FILENAME,
                'ignore'  => array('.', 'ignoreme', 'LC_TEST'),
                'route'   => array('ja' => 'en_US', 'en_US' => 'ja'),
            )
        );

        $translate2 = new Translator\Translator(
            array(
                'adapter' => Translator\Translator::AN_CSV,
                'content' => __DIR__ . '/Adapter/_files/translation_en.csv',
                'locale'  => 'en_US',
            )
        );

        $translate->addTranslation($translate2);

        $langs = $translate->getList();
        $this->assertFalse(array_key_exists('de_DE', $langs));
        $this->assertTrue(array_key_exists('ja', $langs));
        $this->assertTrue(array_key_exists('en_US', $langs));
        $this->assertEquals('Message 5 (en)', $translate->translate('Message 5', 'ja'));
        $this->assertEquals('Message 5 (en)', $translate->translate('Message 5', 'ja'));
    }

    /**
     * ZF-9877
     */
    public function testSetCacheThroughOptions()
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


        $translate = new Translator\Translator(array(
            'adapter' => Translator\Translator::AN_ARRAY,
            'content' => array('msg1' => 'Message 1 (en)'),
            'locale'  => 'en',
            'cache'   => $cache,
        ));

        $return = Translator\Translator::getCache();
        $this->assertTrue($return instanceof CacheAdapter);
        $this->assertTrue(Translator\Translator::hasCache());
    }

    /**
     * @ZF-10051
     */
    public function testSettingLogPriorityForLog()
    {
        $stream = fopen('php://memory', 'w+');
        $writer = new Writer\Stream($stream);
        $log    = new Log\Logger($writer);

        $lang = new Translator\Translator(array(
            'adapter'     => Translator\Translator::AN_CSV,
            'content'     => __DIR__ . '/../_files',
            'locale'      => 'en',
            'delimiter'   => ',',
            'logPriority' => 3,
            'log'         => $log)
        );

        $lang->setLocale('ru');

        rewind($stream);
        $this->assertContains('ERR (3)', stream_get_contents($stream));

        $lang->setOptions(array('logPriority' => 1));
        $lang->setLocale('sv');

        rewind($stream);
        $this->assertContains('ALERT (1)', stream_get_contents($stream));
    }

    /**
     * @ZF-10941
     */
    public function testIgnoreBasePath()
    {
        $lang = new Translator\Translator(
             Translator\Translator::AN_ARRAY,
             __DIR__ . '/Adapter/_files/.iamhidden/testarray',
             'en_GB',
             array('scan' => Translator\Translator::LOCALE_DIRECTORY)
        );

        $this->assertEquals('Message 1 (ja)', $lang->_('Message 1', 'ja'        ));
        $this->assertEquals('Message 1 (en)', $lang->_('Message 1'              ));
        $this->assertEquals(array('de_AT' => 'de_AT', 'en_GB' => 'en_GB', 'ja' => 'ja'), $lang->getList());
    }

    /**
     * @group ZF-10911
     */
    public function testRoutingOnHttpHeader()
    {
        $translate = new Translator\Translator(
            array(
                'adapter'   => Translator\Translator::AN_ARRAY,
                'content'   => __DIR__ . '/Adapter/_files/testarray/',
                'locale'    => 'auto',
                'scan'      => Translator\Translator::LOCALE_FILENAME,
                'ignore'    => array('.', 'ignoreme', 'LC_OTHER'),
                // needed because browser settings can not be simulated in phpunit
                'route'     => array('ja' => 'de_AT', 'de_AT' => 'de_DE', 'de_DE' => 'en_US'),
                'routeHttp' => true,
            )
        );

        $translate2 = new Translator\Translator(
            array(
                'adapter' => Translator\Translator::AN_CSV,
                'content' => __DIR__ . '/Adapter/_files/translation_en.csv',
                'locale'  => 'en_US',
            )
        );

        $translate->addTranslation($translate2);

        $langs = $translate->getList();
        $this->assertFalse(array_key_exists('de_AT', $langs));
        $this->assertTrue(array_key_exists('ja', $langs));
        $this->assertTrue(array_key_exists('en_US', $langs));
        $this->assertEquals('Message 5 (en)', $translate->translate('Message 5', 'ja'));
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
        $this->_errorOccured = true;
    }

    /**
     * Custom callback for testGettingPluralsUsingOwnRule
     *
     * @param  integer $number
     * @return integer
     */
    public function customPlural($number) {
        return 1;
    }
}
