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
 * @package    Zend_Translate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once dirname(__FILE__) . '/../TestHelper.php';

/**
 * Zend_Translate
 */
require_once 'Zend/Translate.php';

/**
 * Zend_Translate_Plural
 */
require_once 'Zend/Translate/Plural.php';

/**
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Translate
 */
class Zend_TranslateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_TranslateTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (Zend_Translate::hasCache()) {
            Zend_Translate::removeCache();
        }

        require_once 'Zend/Translate/Adapter/Array.php';
        if (Zend_Translate_Adapter_Array::hasCache()) {
            Zend_Translate_Adapter_Array::removeCache();
        }
    }

    public function testCreate()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('1' => '1'));
        $this->assertTrue($lang instanceof Zend_Translate);
    }

    public function testLocaleInitialization()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'message1'), 'en');
        $this->assertEquals('en', $lang->getLocale());
    }

    public function testDefaultLocale()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'message1'));
        $defaultLocale = new Zend_Locale();
        $this->assertEquals($defaultLocale->toString(), $lang->getLocale());
    }

    public function testGetAdapter()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY , array('1' => '1'), 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Array);

        $lang = new Zend_Translate(Zend_Translate::AN_GETTEXT , dirname(__FILE__) . '/Translate/Adapter/_files/translation_en.mo', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Gettext);

        $lang = new Zend_Translate(Zend_Translate::AN_TMX , dirname(__FILE__) . '/Translate/Adapter/_files/translation_en.tmx', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Tmx);

        $lang = new Zend_Translate(Zend_Translate::AN_CSV , dirname(__FILE__) . '/Translate/Adapter/_files/translation_en.csv', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Csv);

        $lang = new Zend_Translate(Zend_Translate::AN_XLIFF , dirname(__FILE__) . '/Translate/Adapter/_files/translation_en.xliff', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Xliff);

        $lang = new Zend_Translate('Qt' , dirname(__FILE__) . '/Translate/Adapter/_files/translation_en2.ts', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Qt);

        $lang = new Zend_Translate('XmlTm' , dirname(__FILE__) . '/Translate/Adapter/_files/translation_en.xmltm', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_XmlTm);

        $lang = new Zend_Translate('Tbx' , dirname(__FILE__) . '/Translate/Adapter/_files/translation_en.tbx', 'en');
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Tbx);
    }

    public function testSetAdapter()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_GETTEXT , dirname(__FILE__) . '/Translate/Adapter/_files/translation_en.mo', 'en');
        $lang->setAdapter(Zend_Translate::AN_ARRAY, array('de' => 'de'));
        $this->assertTrue($lang->getAdapter() instanceof Zend_Translate_Adapter_Array);

        try {
            $lang->xxxFunction();
            $this->fail("exception expected");
        } catch (Zend_Translate_Exception $e) {
            // success
        }
    }

    public function testAddTranslation()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');

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
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $this->assertEquals('en', $lang->getLocale());
    }

    public function testSetLocale()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
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
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertEquals('ru', $lang->getLocale());

        $lang->setLocale('en');
        $this->assertEquals('en', $lang->getLocale());
    }

    public function testGetLanguageList()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertEquals(2, count($lang->getList()));
        $this->assertTrue(in_array('en', $lang->getList()));
        $this->assertTrue(in_array('ru', $lang->getList()));
    }

    public function testIsAvailable()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertTrue( $lang->isAvailable('en'));
        $this->assertTrue( $lang->isAvailable('ru'));
        $this->assertFalse($lang->isAvailable('fr'));
    }

    public function testTranslate()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1 (en)'), 'en');
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
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1 (en)'), 'en_US');
        $this->assertTrue( $lang->isTranslated('msg1'             ));
        $this->assertFalse($lang->isTranslated('msg2'             ));
        $this->assertFalse($lang->isTranslated('msg1', false, 'en'));
        $this->assertFalse($lang->isTranslated('msg1', true,  'en'));
        $this->assertFalse($lang->isTranslated('msg1', false, 'ru'));
    }

    public function testWithOption()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_CSV , dirname(__FILE__) . '/Translate/Adapter/_files/translation_otherdelimiter.csv', 'en', array('delimiter' => ','));
        $this->assertEquals('Message 1 (en)', $lang->translate('Message 1'));
        $this->assertEquals('Message 4 (en)', $lang->translate('Message 4,'));
        $this->assertEquals('Message 5, (en)', $lang->translate('Message 5'));
    }

    public function testDirectorySearch()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_CSV, dirname(__FILE__) . '/Translate/Adapter/_files/testcsv', 'de_AT', array('scan' => Zend_Translate::LOCALE_DIRECTORY));
        $this->assertEquals(array('de_AT' => 'de_AT', 'en_GB' => 'en_GB'), $lang->getList());
        $this->assertEquals('Nachricht 8', $lang->translate('Message 8'));
    }

    public function testFileSearch()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_CSV, dirname(__FILE__) . '/Translate/Adapter/_files/testcsv', 'de_DE', array('scan' => Zend_Translate::LOCALE_FILENAME));
        $this->assertEquals(array('de_DE' => 'de_DE', 'en_US' => 'en_US'), $lang->getList());
        $this->assertEquals('Nachricht 8', $lang->translate('Message 8'));
    }

    public function testTestingCacheHandling()
    {
        require_once 'Zend/Cache.php';
        $cache = Zend_Cache::factory('Core', 'File',
            array('lifetime' => 120, 'automatic_serialization' => true),
            array('cache_dir' => dirname(__FILE__) . '/_files/'));
        Zend_Translate::setCache($cache);

        $cache = Zend_Translate::getCache();
        $this->assertTrue($cache instanceof Zend_Cache_Core);
        $this->assertTrue(Zend_Translate::hasCache());

        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1 (en)'), 'en');
        $adapter = $lang->getAdapter();
        $this->assertTrue($adapter instanceof Zend_Translate_Adapter_Array);
        $adaptercache = $adapter->getCache();
        $this->assertTrue($adaptercache instanceof Zend_Cache_Core);

        Zend_Translate::clearCache();
        $this->assertTrue(Zend_Translate::hasCache());
        Zend_Translate::removeCache();
        $this->assertFalse(Zend_Translate::hasCache());
    }

    public function testExceptionWhenNoAdapterClassWasSet()
    {
        try {
            $lang = new Zend_Translate('Zend_Locale', dirname(__FILE__) . '/Translate/_files/test2', null, array('scan' => Zend_Translate::LOCALE_FILENAME));
            $this->fail('Exception due to false adapter class expected');
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('does not extend Zend_Translate_Adapter', $e->getMessage());
        }
    }

    public function testZF3679()
    {
        require_once 'Zend/Locale.php';
        $locale = new Zend_Locale('de_AT');
        require_once 'Zend/Registry.php';
        Zend_Registry::set('Zend_Locale', $locale);

        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'message1'), 'de_AT');
        $this->assertEquals('de_AT', $lang->getLocale());
        Zend_Registry::_unsetInstance();
    }

    /**
     * ZF-4994
     */
    public function testCamelCasedOptions()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_CSV, dirname(__FILE__) . '/Translate/Adapter/_files/translation_otherdelimiter.csv', 'en', array('delimiter' => ','));
        $lang->setOptions(array('myOption' => true));
        $this->assertTrue($lang->getOptions('myOption'));
    }

    /**
     * ZF-4905
     */
    public function testPathNameWithColonResolution()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_CSV, dirname(__FILE__) . '/Translate/Adapter/../Adapter/_files', 'en', array('delimiter' => ','));
        $this->assertEquals('en', $lang->getLocale());
    }

    public function testUntranslatedMessageWithTriggeredError()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_CSV, dirname(__FILE__) . '/Translate/Adapter/_files', 'en', array('delimiter' => ','));
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
        $lang = new Zend_Translate(Zend_Translate::AN_CSV, dirname(__FILE__) . '/Translate/Adapter/_files', 'en', array('delimiter' => ','));
        $this->assertEquals('ignored', $lang->translate('ignored'));

        $stream = fopen('php://memory', 'w+');
        require_once 'Zend/Log/Writer/Stream.php';
        $writer = new Zend_Log_Writer_Stream($stream);
        require_once 'Zend/Log.php';
        $log    = new Zend_Log($writer);

        $lang->setOptions(array('logUntranslated' => true, 'log' => $log));
        $this->assertEquals('ignored', $lang->translate('ignored'));

        rewind($stream);
        $this->assertContains('ignored', stream_get_contents($stream));
    }

    public function testSettingUnknownLocaleWithTriggeredError()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_CSV, dirname(__FILE__) . '/Translate/Adapter/_files', 'en', array('delimiter' => ','));
        $this->_errorOccured = false;
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $lang->setLocale('ru');
        $this->assertEquals('ru', $lang->getLocale('ru'));
        $this->assertTrue($this->_errorOccured);
        restore_error_handler();
    }

    public function testSettingUnknownLocaleWritingToLog()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_CSV, dirname(__FILE__) . '/Translate/Adapter/_files', 'en', array('delimiter' => ','));

        $stream = fopen('php://memory', 'w+');
        require_once 'Zend/Log/Writer/Stream.php';
        $writer = new Zend_Log_Writer_Stream($stream);
        require_once 'Zend/Log.php';
        $log    = new Zend_Log($writer);

        $lang->setOptions(array('log' => $log));
        $lang->setLocale('ru');

        rewind($stream);
        $this->assertContains('has to be added', stream_get_contents($stream));
    }

    public function testSettingNoLogAsLog()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_CSV, dirname(__FILE__) . '/Translate/Adapter/_files', 'en', array('delimiter' => ','));

        try {
            $lang->setOptions(array('log' => 'nolog'));
            $this->fail();
        } catch (Zend_Translate_Exception $e) {
            $this->assertContains('Instance of Zend_Log expected', $e->getMessage());
        }
    }

    public function testSettingUnknownLocaleWritingToSelfDefinedLog()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_CSV, dirname(__FILE__) . '/Translate/Adapter/_files', 'en', array('delimiter' => ','));
        $this->assertEquals('ignored', $lang->translate('ignored'));

        $stream = fopen('php://memory', 'w+');
        require_once 'Zend/Log/Writer/Stream.php';
        $writer = new Zend_Log_Writer_Stream($stream);
        require_once 'Zend/Log.php';
        $log    = new Zend_Log($writer);

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
        require_once 'Zend/Cache.php';
        $cache = Zend_Cache::factory('Core', 'File',
            array('lifetime' => 120, 'automatic_serialization' => true),
            array('cache_dir' => dirname(__FILE__) . '/_files/'));
        Zend_Translate::setCache($cache);

        $lang = new Zend_Translate(Zend_Translate::AN_CSV, dirname(__FILE__) . '/Translate/Adapter/_files', 'en', array('delimiter' => ','));
        $lang->setOptions(array('logMessage' => 'test'));
        $this->assertEquals('test', $lang->getOptions('logMessage'));
        unset($lang);

        $lang2 = new Zend_Translate(Zend_Translate::AN_CSV, dirname(__FILE__) . '/Translate/Adapter/_files', 'en', array('delimiter' => ','));
        $this->assertEquals('test', $lang2->getOptions('logMessage'));
    }

    /**
     * Tests if setting locale as options sets locale
     */
    public function testSetLocaleAsOption()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
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
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $this->assertTrue(is_array($lang->getOptions()));
    }

    /**
     * Tests if setting locale as options sets locale
     */
    public function testGettingUnknownOption()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $this->assertEquals(null, $lang->getOptions('unknown'));
    }

    /**
     * Tests getting of all message ids works
     */
    public function testGettingAllMessageIds()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1', 'msg2' => 'Message 2'), 'en');
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'ru');
        $this->assertEquals(array('msg1'), $lang->getMessageIds());
        $this->assertEquals(array('msg1', 'msg2'), $lang->getMessageIds('en'));
    }

    /**
     * Tests getting of single message ids
     */
    public function testGettingSingleMessageIds()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1', 'msg2' => 'Message 2'), 'en');
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
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1', 'msg2' => 'Message 2'), 'en');
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
        $lang = new Zend_Translate(
            Zend_Translate::AN_ARRAY,
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
        $lang = new Zend_Translate(
            Zend_Translate::AN_ARRAY,
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
     * Tests getting plurals from lowered locale
     */
    public function testGettingPluralsFromUnknownLocale()
    {
        $lang = new Zend_Translate(
            Zend_Translate::AN_ARRAY,
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
        $lang = new Zend_Translate(Zend_Translate::AN_GETTEXT , dirname(__FILE__) . '/Translate/Adapter/_files/translation_en.mo', 'en');

        $this->assertEquals('Message 5 (en) Plural 0', $lang->translate(array('Message 5', 'Message 5 Plural', 1)));
        $this->assertEquals('Message 5 (en) Plural 0', $lang->plural('Message 5', 'Message 5 Plural', 1));
        $this->assertEquals('Message 5 (en) Plural 1', $lang->translate(array('Message 5', 'Message 5 Plural', 2)));
        $this->assertEquals('Message 5 (en) Plural 1', $lang->plural('Message 5', 'Message 5 Plural', 2));
    }

    public function testPluralsWithCsv()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_CSV , dirname(__FILE__) . '/Translate/Adapter/_files/translation_en.csv', 'en');

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
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array('msg1' => 'Message 1'), 'en');
        $this->assertEquals('Message 1', $lang->_('msg1'));

        $lang->addTranslation(array('msg1' => 'Message 1 (en)'), 'en');
        $this->assertEquals('Message 1 (en)', $lang->_('msg1'));
    }

    /**
     * ZF-7560
     */
    public function testUseNumericTranslations()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, array(0 => 'Message 1', 2 => 'Message 2'), 'en');
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
        $lang = new Zend_Translate(Zend_Translate::AN_CSV, dirname(__FILE__) . '/Translate/Adapter/_files', 'en', array('delimiter' => ','));
        $this->assertFalse($lang->isTranslated('ignored'));

        $stream = fopen('php://memory', 'w+');
        require_once 'Zend/Log/Writer/Stream.php';
        $writer = new Zend_Log_Writer_Stream($stream);
        require_once 'Zend/Log.php';
        $log    = new Zend_Log($writer);

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
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, dirname(__FILE__) . '/Translate/Adapter/_files/testarray', 'en_GB', array('scan' => Zend_Translate::LOCALE_DIRECTORY));
        $this->assertEquals('Message 1 (ja)', $lang->_('Message 1', 'ja'        ));
        $this->assertEquals('Message 1 (en)', $lang->_('Message 1'              ));
    }

    /**
     * ZF-7214
     */
    public function testMultiClear()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, dirname(__FILE__) . '/Translate/Adapter/_files/testarray', 'en_GB', array('scan' => Zend_Translate::LOCALE_DIRECTORY));
        $this->assertEquals('Message 1 (ja)', $lang->_('Message 1', 'ja'));
        $lang->addTranslation(dirname(__FILE__) . '/Translate/Adapter/_files/translation_en.php', 'ja', array('clear'));
        $this->assertEquals('Message 1 (en)', $lang->_('Message 1', 'ja'));
    }

    /**
     * ZF-7941
     */
    public function testEmptyTranslation()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, null, null, array('disableNotices' => true));
        $this->assertEquals(0, count($lang->getList()));
    }

    /**
     * Translating Object
     */
    public function testObjectTranslation()
    {
        $lang = new Zend_Translate(Zend_Translate::AN_ARRAY, dirname(__FILE__) . '/Translate/Adapter/_files/testarray', 'en_GB', array('scan' => Zend_Translate::LOCALE_DIRECTORY));
        $this->assertEquals('Message 1 (ja)', $lang->_('Message 1', 'ja'));

        $this->assertEquals($lang, $lang->translate($lang));
    }

    /**
     * Tests getting plurals from lowered locale
     */
    public function testGettingPluralsUsingOwnRule()
    {
        $lang = new Zend_Translate(
            Zend_Translate::AN_ARRAY,
            array('singular' =>
                array('plural_0 (en)',
                    'plural_1 (en)',
                    'plural_2 (en)',
                    'plural_3 (en)'),
                'plural' => ''), 'en'
        );
        $lang->addTranslation(array('msg1' => 'Message 1 (ru)'), 'en_US');
        $lang->setLocale('en_US');

        Zend_Translate_Plural::setPlural(array($this, 'customPlural'), 'en_US');
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
        $translate = new Zend_Translate(
            Zend_Translate::AN_ARRAY,
            array('singular' =>
                array('plural_0 (en)',
                    'plural_1 (en)',
                    'plural_2 (en)',
                    'plural_3 (en)'),
                'plural' => ''), 'en'
        );

        $this->assertFalse($translate->isTranslated('Message 1'));
        $adapter = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/Translate/Adapter/_files/translation_en.mo', 'en');
        $translate->addTranslation($adapter);

        $this->assertTrue($adapter->isTranslated('Message 1'));

        $adapter2 = new Zend_Translate_Adapter_Gettext(dirname(__FILE__) . '/Translate/Adapter/_files/testmo/de_AT/LC_TEST/translation-de_DE.mo', 'de_AT');
        $adapter2->addTranslation(dirname(__FILE__) . '/Translate/Adapter/_files/translation_en2.mo', 'fr');
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
        $translate = new Zend_Translate(
            Zend_Translate::AN_ARRAY,
            dirname(__FILE__) . '/Translate/Adapter/_files/testArray/',
            'auto',
            array(
                'scan' => Zend_Translate::LOCALE_FILENAME,
                'ignore' => array('.', 'ignoreme', 'LC_TEST')
            )
        );

        $langs = $translate->getList();
        $this->assertFalse(array_key_exists('de_DE', $langs));
        $this->assertTrue(array_key_exists('ja', $langs));
        $this->assertTrue(array_key_exists('en_US', $langs));

        $translate2 = new Zend_Translate(
            Zend_Translate::AN_ARRAY,
            dirname(__FILE__) . '/Translate/Adapter/_files/testArray/',
            'auto',
            array(
                'scan' => Zend_Translate::LOCALE_FILENAME,
                'ignore' => array('.', 'regex_1' => '/de_DE/', 'regex' => '/ja/')
            )
        );

        $langs = $translate2->getList();
        $this->assertFalse(array_key_exists('de_DE', $langs));
        $this->assertFalse(array_key_exists('ja', $langs));
        $this->assertTrue(array_key_exists('en_US', $langs));
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

// Call Zend_TranslateTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_TranslateTest::main") {
    Zend_TranslateTest::main();
}
