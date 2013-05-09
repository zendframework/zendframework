<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\I18n\Translator;

use PHPUnit_Framework_TestCase as TestCase;
use Locale;
use Zend\EventManager\EventInterface;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\TextDomain;
use ZendTest\I18n\Translator\TestAsset\Loader as TestLoader;

class TranslatorTest extends TestCase
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var string
     */
    protected $originalLocale;

    /**
     * @var string
     */
    protected $testFilesDir;

    public function setUp()
    {
        $this->originalLocale = Locale::getDefault();
        $this->translator     = new Translator();

        Locale::setDefault('en_EN');

        $this->testFilesDir = __DIR__ . '/_files';
    }

    public function tearDown()
    {
        Locale::setDefault($this->originalLocale);
    }

    public function testFactoryCreatesTranslator()
    {
        $translator = Translator::factory(array(
            'locale' => 'de_DE',
            'patterns' => array(
                array(
                    'type' => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern' => 'translation-%s.php'
                )
            ),
            'files' => array(
                array(
                    'type' => 'phparray',
                    'filename' => $this->testFilesDir . '/translation_en.php',
                )
            )
        ));

        $this->assertInstanceOf('Zend\I18n\Translator\Translator', $translator);
        $this->assertEquals('de_DE', $translator->getLocale());
    }

    public function testTranslationFromSeveralTranslationFiles()
    {
        $translator = Translator::factory(array(
            'locale' => 'de_DE',
            'translation_file_patterns' => array(
                array(
                    'type' => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern' => 'translation-%s.php'
                ),
                array(
                    'type' => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern' => 'translation-more-%s.php'
                )
            )
        ));

        //Test translator instance
        $this->assertInstanceOf('Zend\I18n\Translator\Translator', $translator);

        //Test translations
        $this->assertEquals('Nachricht 1', $translator->translate('Message 1')); //translation-de_DE.php
        $this->assertEquals('Nachricht 9', $translator->translate('Message 9')); //translation-more-de_DE.php
        $this->assertEquals('Nachricht 10 - 0', $translator->translatePlural('Message 10', 'Message 10', 1)); //translation-de_DE.php
        $this->assertEquals('Nachricht 10 - 1', $translator->translatePlural('Message 10', 'Message 10', 2)); //translation-de_DE.php
        $this->assertEquals('Nachricht 11 - 0', $translator->translatePlural('Message 11', 'Message 11', 1)); //translation-more-de_DE.php
        $this->assertEquals('Nachricht 11 - 1', $translator->translatePlural('Message 11', 'Message 11', 2)); //translation-more-de_DE.php
    }

    public function testTranslationFromDifferentSourceTypes()
    {
        $translator = Translator::factory(array(
            'locale' => 'de_DE',
            'translation_file_patterns' => array(
                array(
                    'type'     => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern'  => 'translation-de_DE.php'
                ),
            ),
            'translation_files' => array(
                array(
                    'type'     => 'phparray',
                    'filename' => $this->testFilesDir . '/testarray/translation-more-de_DE.php'
                )
            )
        ));

        $this->assertEquals('Nachricht 1', $translator->translate('Message 1')); //translation-de_DE.php
        $this->assertEquals('Nachricht 9', $translator->translate('Message 9')); //translation-more-de_DE.php
    }

    public function testFactoryCreatesTranslatorWithCache()
    {
        $translator = Translator::factory(array(
            'locale' => 'de_DE',
            'patterns' => array(
                array(
                    'type' => 'phparray',
                    'base_dir' => $this->testFilesDir . '/testarray',
                    'pattern' => 'translation-%s.php'
                )
            ),
            'cache' => array(
                'adapter' => 'memory'
            )
        ));

        $this->assertInstanceOf('Zend\I18n\Translator\Translator', $translator);
        $this->assertInstanceOf('Zend\Cache\Storage\StorageInterface', $translator->getCache());
    }

    public function testDefaultLocale()
    {
        $this->assertEquals('en_EN', $this->translator->getLocale());
    }

    public function testForcedLocale()
    {
        $this->translator->setLocale('de_DE');
        $this->assertEquals('de_DE', $this->translator->getLocale());
    }

    public function testTranslate()
    {
        $loader = new TestLoader();
        $loader->textDomain = new TextDomain(array('foo' => 'bar'));
        $this->translator->getPluginManager()->setService('test', $loader);
        $this->translator->addTranslationFile('test', null);

        $this->assertEquals('bar', $this->translator->translate('foo'));
    }

    public function testTranslationsLoadedFromCache()
    {
        $cache = \Zend\Cache\StorageFactory::factory(array('adapter' => 'memory'));
        $this->translator->setCache($cache);

        $cache->addItem(
            'Zend_I18n_Translator_Messages_' . md5('default' . 'en_EN'),
            new TextDomain(array('foo' => 'bar'))
        );

        $this->assertEquals('bar', $this->translator->translate('foo'));
    }

    public function testTranslationsAreStoredInCache()
    {
        $cache = \Zend\Cache\StorageFactory::factory(array('adapter' => 'memory'));
        $this->translator->setCache($cache);

        $loader = new TestLoader();
        $loader->textDomain = new TextDomain(array('foo' => 'bar'));
        $this->translator->getPluginManager()->setService('test', $loader);
        $this->translator->addTranslationFile('test', null);

        $this->assertEquals('bar', $this->translator->translate('foo'));

        $item = $cache->getItem('Zend_I18n_Translator_Messages_' . md5('default' . 'en_EN'));
        $this->assertInstanceOf('Zend\I18n\Translator\TextDomain', $item);
        $this->assertEquals('bar', $item['foo']);
    }

    public function testTranslatePlurals()
    {
        $this->translator->setLocale('en_EN');
        $this->translator->addTranslationFile(
            'phparray',
            $this->testFilesDir . '/translation_en.php',
            'default',
            'en_EN'
        );

        $pl0 = $this->translator->translatePlural('Message 5', 'Message 5 Plural', 1);
        $pl1 = $this->translator->translatePlural('Message 5', 'Message 5 Plural', 2);
        $pl2 = $this->translator->translatePlural('Message 5', 'Message 5 Plural', 10);

        $this->assertEquals('Message 5 (en) Plural 0', $pl0);
        $this->assertEquals('Message 5 (en) Plural 1', $pl1);
        $this->assertEquals('Message 5 (en) Plural 2', $pl2);
    }

    public function testTranslateNonExistantLocale()
    {
        $this->translator->addTranslationFilePattern(
            'phparray',
            $this->testFilesDir . '/testarray',
            'translation-%s.php'
        );

        // Test that a locale without translations does not cause warnings

        $this->translator->setLocale('es_ES');

        $this->assertEquals('Message 1', $this->translator->translate('Message 1'));
        $this->assertEquals('Message 9', $this->translator->translate('Message 9'));

        $this->translator->setLocale('fr_FR');

        $this->assertEquals('Message 1', $this->translator->translate('Message 1'));
        $this->assertEquals('Message 9', $this->translator->translate('Message 9'));
    }

    public function testEnableDisableEventManger()
    {
        $this->assertFalse($this->translator->isEventManagerEnabled(), 'Default value');

        $this->translator->enableEventManager();
        $this->assertTrue($this->translator->isEventManagerEnabled());

        $this->translator->disableEventManager();
        $this->assertFalse($this->translator->isEventManagerEnabled());
    }

    public function testEnableEventMangerViaFactory()
    {
        $translator = Translator::factory(array(
            'event_manager_enabled' => true
        ));
        $this->assertTrue($translator->isEventManagerEnabled());

        $translator = Translator::factory(array());
        $this->assertFalse($translator->isEventManagerEnabled());
    }

    public function testMissingTranslationEvent()
    {
        $actualEvent = null;

        $this->translator->enableEventManager();
        $this->translator->getEventManager()->attach(Translator::EVENT_MISSING_TRANSLATION, function(EventInterface $event) use (&$actualEvent) {
            $actualEvent = $event;
        });

        $this->translator->translate('foo', 'bar', 'baz');

        $this->assertInstanceOf('Zend\EventManager\Event', $actualEvent);
        $this->assertEquals(
            array(
                'message'     => 'foo',
                'locale'      => 'baz',
                'text_domain' => 'bar',
            ),
            $actualEvent->getParams()
        );

        // But fire no event when disabled
        $actualEvent = null;
        $this->translator->disableEventManager();
        $this->translator->translate('foo', 'bar', 'baz');
        $this->assertNull($actualEvent);
    }

    public function testListenerOnMissingTranslationEventCanReturnString()
    {
        $trigger     = null;
        $doNotTriger = null;

        $this->translator->enableEventManager();
        $this->translator->getEventManager()->attach(Translator::EVENT_MISSING_TRANSLATION, function(EventInterface $event) use (&$trigger) {
            $trigger = true;
        });
        $this->translator->getEventManager()->attach(Translator::EVENT_MISSING_TRANSLATION, function(EventInterface $event) {
            return 'EVENT TRIGGERED';
        });
        $this->translator->getEventManager()->attach(Translator::EVENT_MISSING_TRANSLATION, function(EventInterface $event) use (&$doNotTrigger) {
            $doNotTrigger = true;
        });

        $result = $this->translator->translate('foo', 'bar', 'baz');
        $this->assertTrue($trigger);
        $this->assertEquals('EVENT TRIGGERED', $result);
        $this->assertNull($doNotTrigger);
    }

    public function testNoMessagesLoadedEvent()
    {
        $actualEvent = null;

        $this->translator->enableEventManager();
        $this->translator->getEventManager()->attach(Translator::EVENT_NO_MESSAGES_LOADED, function(EventInterface $event) use (&$actualEvent) {
            $actualEvent = $event;
        });

        $this->translator->translate('foo', 'bar', 'baz');

        $this->assertInstanceOf('Zend\EventManager\Event', $actualEvent);
        $this->assertEquals(
            array(
                'locale'      => 'baz',
                'text_domain' => 'bar',
            ),
            $actualEvent->getParams()
        );

        // But fire no event when disabled
        $actualEvent = null;
        $this->translator->disableEventManager();
        $this->translator->translate('foo', 'bar', 'baz');
        $this->assertNull($actualEvent);
    }

    public function testListenerOnNoMessagesLoadedEventCanReturnTextDomainObject()
    {
        $trigger      = null;
        $doNotTrigger = null;
        $textDomain   = new TextDomain(array(
            'foo' => 'BOOYAH',
        ));

        $this->translator->enableEventManager();
        $events = $this->translator->getEventManager();
        $events->attach(Translator::EVENT_NO_MESSAGES_LOADED, function(EventInterface $event) use (&$trigger) {
            $trigger = true;
        });
        $events->attach(Translator::EVENT_NO_MESSAGES_LOADED, function(EventInterface $event) use ($textDomain) {
            return $textDomain;
        });
        $events->attach(Translator::EVENT_NO_MESSAGES_LOADED, function(EventInterface $event) use (&$doNotTrigger){
            $doNotTrigger = true;
        });

        $result = $this->translator->translate('foo', 'bar', 'baz');

        $this->assertTrue($trigger);
        $this->assertNull($doNotTrigger);
        $this->assertEquals('BOOYAH', $result);
    }
}
