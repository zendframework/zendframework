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
    Zend\Cache\Storage\Adapter as CacheAdapter;

/**
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Locale
 */
class CldrTest extends \PHPUnit_Framework_TestCase
{

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

    /**
     * test for reading with standard locale
     * expected array
     */
    public function testNoLocale()
    {
        $this->assertTrue(is_array(Cldr::getDisplayLanguage(null)));

        try {
            $value = Cldr::getDisplayLanguage('nolocale');
            $this->fail('locale should throw exception');
        } catch (InvalidArgumentException $e) {
            // success
        }

        $locale = new Locale('de');
        $this->assertTrue(is_array(Cldr::getDisplayLanguage($locale)));
    }


    /**
     * test for reading without type
     * expected empty array
     */
    public function testNoType()
    {
        try {
            $value = Cldr::getContent('de','');
            $this->fail('content should throw an exception');
        } catch (InvalidArgumentException $e) {
            // success
        }

        try {
            $value = Cldr::getContent('de','xxxxxxx');
            $this->fail('content should throw an exception');
        } catch (InvalidArgumentException $e) {
            // success
        }
    }


    /**
     * test for reading the languagelist from locale
     * expected array
     */
    public function testLanguage()
    {
        $data = Cldr::getDisplayLanguage('de');
        $this->assertEquals('Deutsch',  $data['de']);
        $this->assertEquals('Englisch', $data['en']);

        $value = Cldr::getDisplayLanguage('de', false, 'de');
        $this->assertEquals('Deutsch', $value);
    }

    /**
     * test for reading the scriptlist from locale
     * expected array
     */
    public function testScript()
    {
        $data = Cldr::getDisplayScript('de_AT');
        $this->assertEquals('Arabisch',   $data['Arab']);
        $this->assertEquals('Lateinisch', $data['Latn']);

        $value = Cldr::getDisplayScript('de_AT', false, 'Arab');
        $this->assertEquals('Arabisch', $value);
    }

    /**
     * test for reading the territorylist from locale
     * expected array
     */
    public function testTerritory()
    {
        $data = Cldr::getDisplayTerritory('de_AT');
        $this->assertEquals('Österreich', $data['AT']);
        $this->assertEquals('Martinique', $data['MQ']);

        $value = Cldr::getDisplayTerritory('de_AT', false, 'AT');
        $this->assertEquals('Österreich', $value);
    }

    /**
     * test for reading the variantlist from locale
     * expected array
     */
    public function testVariant()
    {
        $data = Cldr::getList('de_AT', 'variant');
        $this->assertEquals('Boontling', $data['BOONT']);
        $this->assertEquals('Saho',      $data['SAAHO']);

        $value = Cldr::getContent('de_AT', 'variant', 'POSIX');
        $this->assertEquals('Posix', $value);
    }

    /**
     * test for reading the keylist from locale
     * expected array
     */
    public function testKey()
    {
        $data = Cldr::getList('de_AT', 'key');
        $this->assertEquals('Kalender',   $data['calendar']);
        $this->assertEquals('Sortierung', $data['collation']);

        $value = Cldr::getContent('de_AT', 'key', 'collation');
        $this->assertEquals('Sortierung', $value);
    }

    /**
     * test for reading the typelist from locale
     * expected array
     */
    public function testType()
    {
        $data = Cldr::getList('de_AT', 'type');
        $this->assertEquals('Chinesischer Kalender', $data['chinese']);
        $this->assertEquals('Strichfolge',           $data['stroke']);

        $data = Cldr::getList('de_AT', 'type', 'calendar');
        $this->assertEquals('Chinesischer Kalender', $data['chinese']);
        $this->assertEquals('Japanischer Kalender',  $data['japanese']);

        $value = Cldr::getList('de_AT', 'type', 'chinese');
        $this->assertEquals('Chinesischer Kalender', $value['chinese']);
    }

    /**
     * test for reading layout from locale
     * expected array
     */
    public function testLayout()
    {
        $layout = Cldr::getList('es', 'layout');
        $this->assertEquals("", $layout['lines']);
        $this->assertEquals("", $layout['characters']);
        $this->assertEquals("titlecase-firstword", $layout['inList']);
        $this->assertEquals("lowercase-words",     $layout['currency']);
        $this->assertEquals("mixed",               $layout['dayWidth']);
        $this->assertEquals("lowercase-words",     $layout['fields']);
        $this->assertEquals("lowercase-words",     $layout['keys']);
        $this->assertEquals("lowercase-words",     $layout['languages']);
        $this->assertEquals("lowercase-words",     $layout['long']);
        $this->assertEquals("lowercase-words",     $layout['measurementSystemNames']);
        $this->assertEquals("mixed",               $layout['monthWidth']);
        $this->assertEquals("lowercase-words",     $layout['quarterWidth']);
        $this->assertEquals("lowercase-words",     $layout['scripts']);
        $this->assertEquals("mixed",               $layout['territories']);
        $this->assertEquals("lowercase-words",     $layout['types']);
        $this->assertEquals("mixed",               $layout['variants']);
    }

    /**
     * test for reading characters from locale
     * expected array
     */
    public function testCharacters()
    {
        $char = Cldr::getList('de', 'characters');
        $this->assertEquals("[a ä b c d e f g h i j k l m n o ö p q r s t u ü v w x y z ß]", $char['characters']);
        $this->assertEquals("[á à ă â å ā æ ç é è ĕ ê ë ē í ì ĭ î ï ī ñ ó ò ŏ ô ø ō œ ú ù ŭ û ū ÿ]", $char['auxiliary']);
        $this->assertEquals("[a b c d e f g h i j k l m n o p q r s t u v w x y z]", $char['currencySymbol']);
    }

    /**
     * test for reading delimiters from locale
     * expected array
     */
    public function testDelimiters()
    {
        $quote = Cldr::getList('de', 'delimiters');
        $this->assertEquals("„", $quote['quoteStart']);
        $this->assertEquals("“", $quote['quoteEnd']);
        $this->assertEquals("‚", $quote['quoteStartAlt']);
        $this->assertEquals("‘", $quote['quoteEndAlt']);
    }

    /**
     * test for reading measurement from locale
     * expected array
     */
    public function testMeasurement()
    {
        $measure = Cldr::getList('de', 'measurement');
        $this->assertEquals("001", $measure['metric']);
        $this->assertEquals("LR MM US",  $measure['US']);
        $this->assertEquals("001", $measure['A4']);
        $this->assertEquals("BZ CA CL CO CR GT MX NI PA PH PR SV US VE",  $measure['US-Letter']);
    }

    /**
     * test for reading defaultcalendar from locale
     * expected array
     */
    public function testDefaultCalendar()
    {
        $date = Cldr::getContent('de_AT', 'defaultcalendar');
        $this->assertEquals("gregorian", $date);
    }

    /**
     * test for reading defaultmonthcontext from locale
     * expected array
     */
    public function testDefaultMonthContext()
    {
        $date = Cldr::getContent('de_AT', 'monthcontext');
        $this->assertEquals("format", $date);

        $date = Cldr::getContent('de_AT', 'monthcontext', 'islamic');
        $this->assertEquals("format", $date);
    }

    /**
     * test for reading defaultmonth from locale
     * expected array
     */
    public function testDefaultMonth()
    {
        $date = Cldr::getContent('de_AT', 'defaultmonth');
        $this->assertEquals("wide", $date);

        $date = Cldr::getContent('de_AT', 'defaultmonth', 'islamic');
        $this->assertEquals("wide", $date);
    }

    /**
     * test for reading month from locale
     * expected array
     */
    public function testMonth()
    {
        $date   = Cldr::getList('de_AT', 'months');
        $result = array("context" => "format", "default" => "wide", "format" =>
            array("abbreviated" =>
                array(1 => "Jän",  2 => "Feb", 3 => "Mär", 4 => "Apr", 5 => "Mai",
                      6 => "Jun",  7 => "Jul", 8 => "Aug", 9 => "Sep", 10=> "Okt",
                     11 => "Nov", 12 => "Dez"),
                  "narrow" => array(1 => '1', 2 => '2',  3 => '3',   4 =>  '4', 5 =>   '5', 6 => '6', 7 => '7',
                                    8 => '8', 9 => '9', 10 => '10', 11 => '11', 12 => '12'),
                  "wide" =>
                array(1 => "Jänner"  , 2 => "Februar"   , 3 => "März"  , 4 => "April"    , 5 => "Mai",
                      6 => "Juni"    , 7 => "Juli"      , 8 => "August", 9 => "September", 10=> "Oktober",
                     11 => "November", 12 => "Dezember")
            ),
            "stand-alone" => array("abbreviated" =>
                array(1 => 'Jan',    2 =>     'Feb',  3 =>    'Mär',  4 =>    'Apr',  5 =>    'Mai', 6 => 'Jun', 7 => "Jul",
                      8 => "Aug", 9 => "Sep", 10 => "Okt", 11 => "Nov", 12 => "Dez"),
                  "narrow" =>
                array(1 => "J",  2 => "F",  3 => "M",  4 => "A", 5 => "M", 6 => "J",  7 => "J" , 8 => "A",
                      9 => "S", 10 => "O", 11 => "N", 12 => "D"),
                  "wide" => array(1 => '1', 2 => '2',  3 => '3',   4 =>  '4', 5 =>   '5', 6 => '6', 7 => '7',
                                  8 => '8', 9 => '9', 10 => '10', 11 => '11', 12 => '12'),
            ));
        $this->assertEquals($result, $date);

        $date   = Cldr::getList('de_AT', 'months', 'islamic');
        $result = array("context" => "format", "default" => "wide", "format" =>
            array("abbreviated" =>
                array(1 => "Muh."  , 2 => "Saf.", 3 => "Rab. I"  , 4 => "Rab. II"    , 5 => "Jum. I",
                      6 => "Jum. II" , 7 => "Raj.", 8 => "Sha.", 9 => "Ram.", 10=> "Shaw.",
                     11 => "Dhuʻl-Q.", 12 => "Dhuʻl-H."),
                  "narrow" => array(1 => '1', 2 => '2',  3 => '3',   4 =>  '4', 5 =>   '5', 6 => '6', 7 => '7',
                                    8 => '8', 9 => '9', 10 => '10', 11 => '11', 12 => '12'),
                  "wide" =>
                array(1 => "Muharram"  , 2 => "Safar", 3 => "Rabiʻ I"  , 4 => "Rabiʻ II"    , 5 => "Jumada I",
                      6 => "Jumada II" , 7 => "Rajab", 8 => "Shaʻban", 9 => "Ramadan", 10=> "Shawwal",
                     11 => "Dhuʻl-Qiʻdah", 12 => "Dhuʻl-Hijjah")
            ),
            "stand-alone" => array("abbreviated" =>
                array(1 => "Muh."  , 2 => "Saf.", 3 => "Rab. I"  , 4 => "Rab. II"    , 5 => "Jum. I",
                      6 => "Jum. II" , 7 => "Raj.", 8 => "Sha.", 9 => "Ram.", 10=> "Shaw.",
                     11 => "Dhuʻl-Q.", 12 => "Dhuʻl-H."),
                  "narrow" => array(1 => '1', 2 => '2',  3 => '3',   4 =>  '4', 5 =>   '5', 6 => '6', 7 => '7',
                                  8 => '8', 9 => '9', 10 => '10', 11 => '11', 12 => '12'),
                  "wide" =>
                array(1 => "Muharram"  , 2 => "Safar", 3 => "Rabiʻ I"  , 4 => "Rabiʻ II"    , 5 => "Jumada I",
                      6 => "Jumada II" , 7 => "Rajab", 8 => "Shaʻban", 9 => "Ramadan", 10=> "Shawwal",
                     11 => "Dhuʻl-Qiʻdah", 12 => "Dhuʻl-Hijjah")
            ));
        $this->assertEquals($result, $date);

        $date = Cldr::getList('de_AT', 'month');
        $this->assertEquals(array(1 => "Jänner"  , 2 => "Februar"   , 3 => "März"  , 4 => "April"    , 5 => "Mai",
                                  6 => "Juni"    , 7 => "Juli"      , 8 => "August", 9 => "September", 10=> "Oktober",
                                 11 => "November", 12 => "Dezember"), $date);

        $date = Cldr::getList('de_AT', 'month', array('gregorian', 'format', 'wide'));
        $this->assertEquals(array(1 => "Jänner"  , 2 => "Februar"   , 3 => "März"  , 4 => "April"    , 5 => "Mai",
                                  6 => "Juni"    , 7 => "Juli"      , 8 => "August", 9 => "September", 10=> "Oktober",
                                 11 => "November", 12 => "Dezember"), $date);

        $value = Cldr::getContent('de_AT', 'month', 12);
        $this->assertEquals('Dezember', $value);

        $value = Cldr::getContent('de_AT', 'month', array('gregorian', 'format', 'wide', 12));
        $this->assertEquals('Dezember', $value);

        $value = Cldr::getContent('ar', 'month', array('islamic', 'format', 'wide', 1));
        $this->assertEquals("محرم", $value);
    }

    /**
     * test for reading defaultdaycontext from locale
     * expected array
     */
    public function testDefaultDayContext()
    {
        $date = Cldr::getContent('de_AT', 'daycontext');
        $this->assertEquals("format", $date);

        $date = Cldr::getContent('de_AT', 'daycontext', 'islamic');
        $this->assertEquals("format", $date);
    }

    /**
     * test for reading defaultday from locale
     * expected array
     */
    public function testDefaultDay()
    {
        $date = Cldr::getContent('de_AT', 'defaultday');
        $this->assertEquals("wide", $date);

        $date = Cldr::getContent('de_AT', 'defaultday', 'islamic');
        $this->assertEquals("wide", $date);
    }

    /**
     * test for reading day from locale
     * expected array
     */
    public function testDay()
    {
        $date = Cldr::getList('de_AT', 'days');
        $result = array("context" => "format", "default" => "wide", "format" =>
            array("abbreviated" => array("sun" => "So.", "mon" => "Mo.", "tue" => "Di.", "wed" => "Mi.",
                      "thu" => "Do.", "fri" => "Fr.", "sat" => "Sa."),
                  "narrow" => array("sun" => "1", "mon" => "2", "tue" => "3", "wed" => "4",
                      "thu" => "5", "fri" => "6", "sat" => "7"),
                  "wide" => array("sun" => "Sonntag", "mon" => "Montag", "tue" => "Dienstag",
                      "wed" => "Mittwoch", "thu" => "Donnerstag", "fri" => "Freitag", "sat" => "Samstag")
            ),
            "stand-alone" => array("abbreviated" => array("sun" => "So", "mon" => "Mo", "tue" => "Di", "wed" => "Mi",
                      "thu" => "Do", "fri" => "Fr", "sat" => "Sa"),
                  "narrow" => array("sun" => "S", "mon" => "M", "tue" => "D", "wed" => "M",
                      "thu" => "D", "fri" => "F", "sat" => "S"),
                  "wide" => array("sun" => "1", "mon" => "2", "tue" => "3", "wed" => "4",
                      "thu" => "5", "fri" => "6", "sat" => "7")
            ));
        $this->assertEquals($result, $date);

        $date = Cldr::getList('de_AT', 'days', 'islamic');
        $result = array("context" => "format", "default" => "wide", "format" =>
            array("abbreviated" => array("sun" => "1", "mon" => "2", "tue" => "3", "wed" => "4",
                      "thu" => "5", "fri" => "6", "sat" => "7"),
                  "narrow" => array("sun" => "1", "mon" => "2", "tue" => "3", "wed" => "4",
                      "thu" => "5", "fri" => "6", "sat" => "7"),
                  "wide" => array("sun" => "1", "mon" => "2", "tue" => "3", "wed" => "4",
                      "thu" => "5", "fri" => "6", "sat" => "7")
            ),
            "stand-alone" => array("abbreviated" => array("sun" => "1", "mon" => "2", "tue" => "3", "wed" => "4",
                      "thu" => "5", "fri" => "6", "sat" => "7"),
                  "narrow" => array("sun" => "1", "mon" => "2", "tue" => "3", "wed" => "4",
                      "thu" => "5", "fri" => "6", "sat" => "7"),
                  "wide" => array("sun" => "1", "mon" => "2", "tue" => "3", "wed" => "4",
                      "thu" => "5", "fri" => "6", "sat" => "7")
            ));
        $this->assertEquals($result, $date);

        $date = Cldr::getList('de_AT', 'day');
        $this->assertEquals(array("sun" => "Sonntag", "mon" => "Montag", "tue" => "Dienstag",
                      "wed" => "Mittwoch", "thu" => "Donnerstag", "fri" => "Freitag", "sat" => "Samstag"), $date);

        $date = Cldr::getList('de_AT', 'day', array('gregorian', 'format', 'wide'));
        $this->assertEquals(array("sun" => "Sonntag", "mon" => "Montag", "tue" => "Dienstag",
                      "wed" => "Mittwoch", "thu" => "Donnerstag", "fri" => "Freitag", "sat" => "Samstag"), $date);

        $value = Cldr::getContent('de_AT', 'day', 'mon');
        $this->assertEquals('Montag', $value);

        $value = Cldr::getContent('de_AT', 'day', array('gregorian', 'format', 'wide', 'mon'));
        $this->assertEquals('Montag', $value);

        $value = Cldr::getContent('ar', 'day', array('islamic', 'format', 'wide', 'mon'));
        $this->assertEquals("2", $value);
    }

    /**
     * test for reading quarter from locale
     * expected array
     */
    public function testQuarter()
    {
        $date = Cldr::getList('de_AT', 'quarters');
        $result = array("format" =>
            array("abbreviated" => array("1" => "Q1", "2" => "Q2", "3" => "Q3", "4" => "Q4"),
                  "narrow" => array("1" => "1", "2" => "2", "3" => "3", "4" => "4"),
                  "wide" => array("1" => "1. Quartal", "2" => "2. Quartal", "3" => "3. Quartal",
                      "4" => "4. Quartal")
            ),
            "stand-alone" => array("abbreviated" => array("1" => "Q1", "2" => "Q2", "3" => "Q3", "4" => "Q4"),
                  "narrow" => array("1" => "1", "2" => "2", "3" => "3", "4" => "4"),
                  "wide" => array("1" => "Q1", "2" => "Q2", "3" => "Q3", "4" => "Q4")
            ));
        $this->assertEquals($result, $date);

        $date = Cldr::getList('de_AT', 'quarters', 'islamic');
        $result = array("format" =>
            array("abbreviated" => array("1" => "Q1", "2" => "Q2", "3" => "Q3", "4" => "Q4"),
                  "narrow" => array("1" => "1", "2" => "2", "3" => "3", "4" => "4"),
                  "wide" => array("1" => "Q1", "2" => "Q2", "3" => "Q3",
                      "4" => "Q4")
            ),
            "stand-alone" => array("abbreviated" => array("1" => "Q1", "2" => "Q2", "3" => "Q3", "4" => "Q4"),
                  "narrow" => array("1" => "1", "2" => "2", "3" => "3", "4" => "4"),
                  "wide" => array("1" => "Q1", "2" => "Q2", "3" => "Q3", "4" => "Q4")
            ));
        $this->assertEquals($result, $date);

        $date = Cldr::getList('de_AT', 'quarter');
        $this->assertEquals(array("1" => "1. Quartal", "2" => "2. Quartal", "3" => "3. Quartal",
                      "4" => "4. Quartal"), $date);

        $date = Cldr::getList('de_AT', 'quarter', array('gregorian', 'format', 'wide'));
        $this->assertEquals(array("1" => "1. Quartal", "2" => "2. Quartal", "3" => "3. Quartal",
                      "4" => "4. Quartal"), $date);

        $value = Cldr::getContent('de_AT', 'quarter', '1');
        $this->assertEquals('1. Quartal', $value);

        $value = Cldr::getContent('de_AT', 'quarter', array('gregorian', 'format', 'wide', '1'));
        $this->assertEquals('1. Quartal', $value);

        $value = Cldr::getContent('ar', 'quarter', array('islamic', 'format', 'wide', '1'));
        $this->assertEquals("Q1", $value);
    }

    /**
     * test for reading week from locale
     * expected array
     */
    public function testWeek()
    {
        $value = Cldr::getList('de_AT', 'week');
        $this->assertEquals(array('minDays' => 4, 'firstDay' => 'mon', 'weekendStart' => 'sat',
                                  'weekendEnd' => 'sun'), $value);

        $value = Cldr::getList('en_US', 'week');
        $this->assertEquals(array('minDays' => '4', 'firstDay' => 'sun', 'weekendStart' => 'sat',
                                  'weekendEnd' => 'sun'), $value);
    }

    /**
     * test for reading am from locale
     * expected array
     */
    public function ztestAm()
    {
        $date = Cldr::getContent('de_AT', 'am');
        $this->assertEquals("vorm.", $date);

        $date = Cldr::getContent('de_AT', 'am', 'islamic');
        $this->assertEquals("vorm.", $date);
    }

    /**
     * test for reading pm from locale
     * expected array
     */
    public function ztestPm()
    {
        $date = Cldr::getContent('de_AT', 'pm');
        $this->assertEquals("nachm.", $date);

        $date = Cldr::getContent('de_AT', 'pm', 'islamic');
        $this->assertEquals("nachm.", $date);
    }

    /**
     * test for reading era from locale
     * expected array
     */
    public function testEra()
    {
        $date = Cldr::getList('de_AT', 'eras');
        $result = array(
            "abbreviated" => array("0" => "v. Chr.", "1" => "n. Chr."),
            "narrow" => array("0" => "BCE", "1" => "CE"),
            "names" => array("0" => "v. Chr.", "1" => "n. Chr.")
            );
        $this->assertEquals($result, $date);

        $date = Cldr::getList('de_AT', 'eras', 'islamic');
        $result = array("abbreviated" => array("0" => "AH"), "narrow" => array("0" => "AH"), "names" => array("0" => "AH"));
        $this->assertEquals($result, $date);

        $date = Cldr::getList('de_AT', 'era');
        $this->assertEquals(array("0" => "v. Chr.", "1" => "n. Chr."), $date);

        $date = Cldr::getList('de_AT', 'era', array('gregorian', 'Abbr'));
        $this->assertEquals(array("0" => "v. Chr.", "1" => "n. Chr."), $date);

        $value = Cldr::getContent('de_AT', 'era', '1');
        $this->assertEquals('n. Chr.', $value);

        $value = Cldr::getContent('de_AT', 'era', array('gregorian', 'Names', '1'));
        $this->assertEquals('n. Chr.', $value);

        $value = Cldr::getContent('ar', 'era', array('islamic', 'Abbr', '0'));
        $this->assertEquals('هـ', $value);
    }

    /**
     * test for reading defaultdate from locale
     * expected array
     */
    public function testDefaultDate()
    {
        $value = Cldr::getContent('de_AT', 'defaultdate');
        $this->assertEquals("medium", $value);

        $value = Cldr::getContent('de_AT', 'defaultdate', 'gregorian');
        $this->assertEquals("medium", $value);
    }

    /**
     * test for reading era from locale
     * expected array
     */
    public function testDate()
    {
        $date = Cldr::getList('de_AT', 'date');
        $result = array("full" => "EEEE, dd. MMMM y", "long" => "dd. MMMM y",
                        "medium" => "dd.MM.yyyy", "short" => "dd.MM.yy");
        $this->assertEquals($result, $date);

        $date = Cldr::getList('de_AT', 'date', 'islamic');
        $result = array("full" => "EEEE d. MMMM y G", "long" => "d. MMMM y G",
                        "medium" => "d. MMM y G", "short" => "d.M.y G");
        $this->assertEquals($result, $date);

        $value = Cldr::getContent('de_AT', 'date');
        $this->assertEquals("dd.MM.yyyy", $value);

        $value = Cldr::getContent('de_AT', 'date', 'long');
        $this->assertEquals("dd. MMMM y", $value);

        $value = Cldr::getContent('ar', 'date', array('islamic', 'long'));
        $this->assertEquals("y MMMM d", $value);
    }

    /**
     * test for reading defaulttime from locale
     * expected array
     */
    public function testDefaultTime()
    {
        $value = Cldr::getContent('de_AT', 'defaulttime');
        $this->assertEquals("medium", $value);

        $value = Cldr::getContent('de_AT', 'defaulttime', 'gregorian');
        $this->assertEquals("medium", $value);
    }

    /**
     * test for reading time from locale
     * expected array
     */
    public function testTime()
    {
        $date = Cldr::getList('de_AT', 'time');
        $result = array("full" => "HH:mm:ss zzzz", "long" => "HH:mm:ss z",
                        "medium" => "HH:mm:ss", "short" => "HH:mm");
        $this->assertEquals($result, $date);

        $date = Cldr::getList('de_AT', 'time', 'islamic');
        $result = array("full" => "HH:mm:ss zzzz", "long" => "HH:mm:ss z",
                        "medium" => "HH:mm:ss", "short" => "HH:mm");
        $this->assertEquals($result, $date);

        $value = Cldr::getContent('de_AT', 'time');
        $this->assertEquals("HH:mm:ss", $value);

        $value = Cldr::getContent('de_AT', 'time', 'long');
        $this->assertEquals("HH:mm:ss z", $value);

        $value = Cldr::getContent('ar', 'time', array('islamic', 'long'));
        $this->assertEquals("HH:mm:ss z", $value);
    }

    /**
     * test for reading datetime from locale
     * expected array
     */
    public function testDateTime()
    {
        $value = Cldr::getList('de_AT', 'datetime');
        $result = array(
            'full' => 'EEEE, dd. MMMM y HH:mm:ss zzzz',
            'long' => 'dd. MMMM y HH:mm:ss z',
            'medium' => 'dd.MM.yyyy HH:mm:ss',
            'short' => 'dd.MM.yy HH:mm'
        );
        $this->assertEquals($result, $value);

        $value = Cldr::getList('de_AT', 'datetime', 'gregorian');
        $result = array(
            'full' => 'EEEE, dd. MMMM y HH:mm:ss zzzz',
            'long' => 'dd. MMMM y HH:mm:ss z',
            'medium' => 'dd.MM.yyyy HH:mm:ss',
            'short' => 'dd.MM.yy HH:mm'
        );
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'datetime', 'full');
        $this->assertEquals("EEEE, dd. MMMM y HH:mm:ss zzzz", $value);

        $value = Cldr::getContent('de_AT', 'datetime', array('gregorian', 'long'));
        $this->assertEquals("dd. MMMM y HH:mm:ss z", $value);
    }

    /**
     * test for reading field from locale
     * expected array
     */
    public function testField()
    {
        $value = Cldr::getList('de_AT', 'field');
        $this->assertEquals(array("era" => "Epoche", "year" => "Jahr", "month" => "Monat", "week" => "Woche",
            "day" => "Tag", "weekday" => "Wochentag", "dayperiod" => "Tageshälfte", "hour" => "Stunde",
            "minute" => "Minute", "second" => "Sekunde", "zone" => "Zone"), $value);

        $value = Cldr::getList('de_AT', 'field', 'gregorian');
        $this->assertEquals(array("era" => "Epoche", "year" => "Jahr", "month" => "Monat", "week" => "Woche",
            "day" => "Tag", "weekday" => "Wochentag", "dayperiod" => "Tageshälfte", "hour" => "Stunde",
            "minute" => "Minute", "second" => "Sekunde", "zone" => "Zone"), $value);

        $value = Cldr::getContent('de_AT', 'field', 'week');
        $this->assertEquals("Woche", $value);

        $value = Cldr::getContent('de_AT', 'field', array('gregorian', 'week'));
        $this->assertEquals("Woche", $value);
    }

    /**
     * test for reading relative from locale
     * expected array
     */
    public function testRelative()
    {
        $value = Cldr::getList('de_AT', 'relative');
        $this->assertEquals(array("0" => "Heute", "1" => "Morgen", "2" => "Übermorgen",
            "3" => "In drei Tagen", "-1" => "Gestern", "-2" => "Vorgestern", "-3" => "Vor drei Tagen"), $value);

        $value = Cldr::getList('de_AT', 'relative', 'gregorian');
        $this->assertEquals(array("0" => "Heute", "1" => "Morgen", "2" => "Übermorgen",
            "3" => "In drei Tagen", "-1" => "Gestern", "-2" => "Vorgestern", '-3' => 'Vor drei Tagen'), $value);

        $value = Cldr::getContent('de_AT', 'relative', '-1');
        $this->assertEquals("Gestern", $value);

        $value = Cldr::getContent('de_AT', 'relative', array('gregorian', '-1'));
        $this->assertEquals("Gestern", $value);
    }

    /**
     * test for reading symbols from locale
     * expected array
     */
    public function testSymbols()
    {
        $value = Cldr::getList('de_AT', 'symbols');
        $result = array(    "decimal"  => ",", "group" => ".", "list"  => ";", "percent"  => "%",
            "zero"  => "0", "pattern"  => "#", "plus"  => "+", "minus" => "-", "exponent" => "E",
            "mille" => "‰", "infinity" => "∞", "nan"   => "NaN");
        $this->assertEquals($result, $value);
    }

    /**
     * test for reading decimalnumber from locale
     * expected array
     */
    public function testDecimalNumber()
    {
        $value = Cldr::getContent('de_AT', 'decimalnumber');
        $this->assertEquals("#,##0.###", $value);
    }

    /**
     * test for reading scientificnumber from locale
     * expected array
     */
    public function testScientificNumber()
    {
        $value = Cldr::getContent('de_AT', 'scientificnumber');
        $this->assertEquals("#E0", $value);
    }

    /**
     * test for reading percentnumber from locale
     * expected array
     */
    public function testPercentNumber()
    {
        $value = Cldr::getContent('de_AT', 'percentnumber');
        $this->assertEquals("#,##0 %", $value);
    }

    /**
     * test for reading currencynumber from locale
     * expected array
     */
    public function testCurrencyNumber()
    {
        $value = Cldr::getContent('de_AT', 'currencynumber');
        $this->assertEquals("¤ #,##0.00", $value);
    }

    /**
     * test for reading nametocurrency from locale
     * expected array
     */
    public function testNameToCurrency()
    {
        $value = Cldr::getList('de_AT', 'nametocurrency');
        $result = array(
            'ADP' => 'Andorranische Pesete', 'AED' => 'UAE Dirham', 'AFA' => 'Afghani (1927-2002)',
            'AFN' => 'Afghani', 'ALL' => 'Lek', 'AMD' => 'Dram', 'ANG' => 'Niederl. Antillen Gulden',
            'AOA' => 'Kwanza', 'AOK' => 'Angolanischer Kwanza (1977-1990)', 'AON' => 'Neuer Kwanza',
            'AOR' => 'Kwanza Reajustado', 'ARA' => 'Argentinischer Austral',
            'ARP' => 'Argentinischer Peso (1983-1985)', 'ARS' => 'Argentinischer Peso',
            'ATS' => 'Österreichischer Schilling', 'AUD' => 'Australischer Dollar', 'AWG' => 'Aruba Florin',
            'AZM' => 'Aserbaidschan-Manat (1993-2006)', 'AZN' => 'Aserbaidschan-Manat',
            'BAD' => 'Bosnien und Herzegowina Dinar', 'BAM' => 'Konvertierbare Mark',
            'BBD' => 'Barbados-Dollar', 'BDT' => 'Taka', 'BEC' => 'Belgischer Franc (konvertibel)',
            'BEF' => 'Belgischer Franc', 'BEL' => 'Belgischer Finanz-Franc', 'BGL' => 'Lew (1962-1999)',
            'BGN' => 'Lew', 'BHD' => 'Bahrain-Dinar', 'BIF' => 'Burundi-Franc', 'BMD' => 'Bermuda-Dollar',
            'BND' => 'Brunei-Dollar', 'BOB' => 'Boliviano', 'BOP' => 'Bolivianischer Peso', 'BOV' => 'Mvdol',
            'BRB' => 'Brasilianischer Cruzeiro Novo (1967-1986)', 'BRC' => 'Brasilianischer Cruzado',
            'BRE' => 'Brasilianischer Cruzeiro (1990-1993)', 'BRL' => 'Real',
            'BRN' => 'Brasilianischer Cruzado Novo', 'BRR' => 'Brasilianischer Cruzeiro',
            'BSD' => 'Bahama-Dollar', 'BTN' => 'Ngultrum', 'BUK' => 'Birmanischer Kyat', 'BWP' => 'Pula',
            'BYB' => 'Belarus Rubel (alt)', 'BYR' => 'Belarus Rubel (neu)', 'BZD' => 'Belize-Dollar',
            'CAD' => 'Kanadischer Dollar', 'CDF' => 'Franc congolais', 'CHE' => 'WIR-Euro',
            'CHF' => 'Schweizer Franken', 'CHW' => 'WIR Franken', 'CLF' => 'Unidades de Fomento',
            'CLP' => 'Chilenischer Peso', 'CNY' => 'Renminbi Yuan', 'COP' => 'Kolumbianischer Peso',
            'COU' => 'Unidad de Valor Real', 'CRC' => 'Costa Rica Colon', 'CSD' => 'Alter Serbischer Dinar',
            'CSK' => 'Tschechoslowakische Krone', 'CUC' => 'Kubanischer Peso (konvertibel)', 'CUP' => 'Kubanischer Peso', 'CVE' => 'Kap Verde Escudo',
            'CYP' => 'Zypern-Pfund', 'CZK' => 'Tschechische Krone', 'DDM' => 'Mark der DDR',
            'DEM' => 'Deutsche Mark', 'DJF' => 'Dschibuti-Franc', 'DKK' => 'Dänische Krone',
            'DOP' => 'Dominikanischer Peso', 'DZD' => 'Algerischer Dinar', 'ECS' => 'Ecuadorianischer Sucre',
            'ECV' => 'Verrechnungseinheit für EC', 'EEK' => 'Estnische Krone', 'EGP' => 'Ägyptisches Pfund',
            'ERN' => 'Nakfa', 'ESA' => 'Spanische Peseta (A-Konten)',
            'ESB' => 'Spanische Peseta (konvertibel)', 'ESP' => 'Spanische Peseta', 'ETB' => 'Birr',
            'EUR' => 'Euro', 'FIM' => 'Finnische Mark', 'FJD' => 'Fidschi-Dollar', 'FKP' => 'Falkland-Pfund',
            'FRF' => 'Französischer Franc', 'GBP' => 'Pfund Sterling', 'GEK' => 'Georgischer Kupon Larit',
            'GEL' => 'Georgischer Lari', 'GHC' => 'Cedi', 'GHS' => 'Ghanaische Cedi', 'GIP' => 'Gibraltar-Pfund', 'GMD' => 'Dalasi',
            'GNF' => 'Guinea-Franc', 'GNS' => 'Guineischer Syli', 'GQE' => 'Ekwele',
            'GRD' => 'Griechische Drachme', 'GTQ' => 'Quetzal', 'GWE' => 'Portugiesisch Guinea Escudo',
            'GWP' => 'Guinea Bissau Peso', 'GYD' => 'Guyana-Dollar', 'HKD' => 'Hongkong-Dollar',
            'HNL' => 'Lempira', 'HRD' => 'Kroatischer Dinar', 'HRK' => 'Kuna', 'HTG' => 'Gourde',
            'HUF' => 'Forint', 'IDR' => 'Rupiah', 'IEP' => 'Irisches Pfund', 'ILP' => 'Israelisches Pfund',
            'ILS' => 'Schekel', 'INR' => 'Indische Rupie', 'IQD' => 'Irak Dinar', 'IRR' => 'Rial',
            'ISK' => 'Isländische Krone', 'ITL' => 'Italienische Lira', 'JMD' => 'Jamaika-Dollar',
            'JOD' => 'Jordanischer Dinar', 'JPY' => 'Yen', 'KES' => 'Kenia-Schilling', 'KGS' => 'Som',
            'KHR' => 'Riel', 'KMF' => 'Komoren Franc', 'KPW' => 'Nordkoreanischer Won',
            'KRW' => 'Südkoreanischer Won', 'KWD' => 'Kuwait Dinar', 'KYD' => 'Kaiman-Dollar',
            'KZT' => 'Tenge', 'LAK' => 'Kip', 'LBP' => 'Libanesisches Pfund', 'LKR' => 'Sri Lanka Rupie',
            'LRD' => 'Liberianischer Dollar', 'LSL' => 'Loti', 'LTL' => 'Litauischer Litas',
            'LTT' => 'Litauischer Talonas', 'LUC' => 'Luxemburgischer Franc (konvertibel)',
            'LUF' => 'Luxemburgischer Franc', 'LUL' => 'Luxemburgischer Finanz-Franc',
            'LVL' => 'Lettischer Lats', 'LVR' => 'Lettischer Rubel', 'LYD' => 'Libyscher Dinar',
            'MAD' => 'Marokkanischer Dirham', 'MAF' => 'Marokkanischer Franc', 'MDL' => 'Moldau Leu',
            'MGA' => 'Madagaskar Ariary', 'MGF' => 'Madagaskar-Franc', 'MKD' => 'Denar',
            'MLF' => 'Malischer Franc', 'MMK' => 'Kyat', 'MNT' => 'Tugrik', 'MOP' => 'Pataca',
            'MRO' => 'Ouguiya', 'MTL' => 'Maltesische Lira', 'MTP' => 'Maltesisches Pfund',
            'MUR' => 'Mauritius-Rupie', 'MVR' => 'Rufiyaa', 'MWK' => 'Malawi Kwacha',
            'MXN' => 'Mexikanischer Peso', 'MXP' => 'Mexikanischer Silber-Peso (1861-1992)',
            'MXV' => 'Mexican Unidad de Inversion (UDI)', 'MYR' => 'Malaysischer Ringgit',
            'MZE' => 'Mosambikanischer Escudo', 'MZM' => 'Alter Metical', 'MZN' => 'Metical',
            'NAD' => 'Namibia-Dollar', 'NGN' => 'Naira', 'NIC' => 'Cordoba', 'NIO' => 'Gold-Cordoba',
            'NLG' => 'Holländischer Gulden', 'NOK' => 'Norwegische Krone', 'NPR' => 'Nepalesische Rupie',
            'NZD' => 'Neuseeland-Dollar', 'OMR' => 'Rial Omani', 'PAB' => 'Balboa',
            'PEI' => 'Peruanischer Inti', 'PEN' => 'Neuer Sol', 'PES' => 'Sol', 'PGK' => 'Kina',
            'PHP' => 'Philippinischer Peso', 'PKR' => 'Pakistanische Rupie', 'PLN' => 'Zloty',
            'PLZ' => 'Zloty (1950-1995)', 'PTE' => 'Portugiesischer Escudo', 'PYG' => 'Guarani',
            'QAR' => 'Katar Riyal', 'RHD' => 'Rhodesischer Dollar', 'ROL' => 'Leu', 'RON' => 'Rumänischer Leu',
            'RSD' => 'Serbischer Dinar', 'RUB' => 'Russischer Rubel (neu)', 'RUR' => 'Russischer Rubel (alt)',
            'RWF' => 'Ruanda-Franc', 'SAR' => 'Saudi Riyal', 'SBD' => 'Salomonen-Dollar',
            'SCR' => 'Seychellen-Rupie', 'SDD' => 'Sudanesischer Dinar', 'SDG' => 'Sudanesisches Pfund', 'SDP' => 'Sudanesisches Pfund (alt)',
            'SEK' => 'Schwedische Krone', 'SGD' => 'Singapur-Dollar', 'SHP' => 'St. Helena Pfund',
            'SIT' => 'Tolar', 'SKK' => 'Slowakische Krone', 'SLL' => 'Leone', 'SOS' => 'Somalia-Schilling',
            'SRD' => 'Surinamischer Dollar', 'SRG' => 'Suriname Gulden', 'STD' => 'Dobra',
            'SUR' => 'Sowjetischer Rubel', 'SVC' => 'El Salvador Colon', 'SYP' => 'Syrisches Pfund',
            'SZL' => 'Lilangeni', 'THB' => 'Baht', 'TJR' => 'Tadschikistan Rubel',
            'TJS' => 'Tadschikistan Somoni', 'TMM' => 'Turkmenistan-Manat',
            'TMT' => 'Neuer Turkmenistan-Manat', 'TND' => 'Tunesischer Dinar',
            'TOP' => 'Paʻanga', 'TPE' => 'Timor-Escudo', 'TRL' => 'Alte Türkische Lira',
            'TRY' => 'Türkische Lira', 'TTD' => 'Trinidad- und Tobago-Dollar',
            'TWD' => 'Neuer Taiwan-Dollar', 'TZS' => 'Tansania-Schilling', 'UAH' => 'Hryvnia',
            'UAK' => 'Ukrainischer Karbovanetz', 'UGS' => 'Uganda-Schilling (1966-1987)',
            'UGX' => 'Uganda-Schilling', 'USD' => 'US-Dollar', 'USN' => 'US Dollar (Nächster Tag)',
            'USS' => 'US Dollar (Gleicher Tag)', 'UYI' => 'UYU', 'UYP' => 'Uruguayischer Neuer Peso (1975-1993)',
            'UYU' => 'Uruguayischer Peso', 'UZS' => 'Usbekistan Sum', 'VEB' => 'Bolivar', 'VEF' => 'Bolívar Fuerte', 'VND' => 'Dong',
            'VUV' => 'Vatu', 'WST' => 'Tala', 'XAF' => 'CFA Franc (Äquatorial)', 'XAG' => 'Unze Silber',
            'XAU' => 'Unze Gold', 'XBA' => 'Europäische Rechnungseinheit',
            'XBB' => 'Europäische Währungseinheit (XBB)', 'XBC' => 'Europäische Rechnungseinheit (XBC)',
            'XBD' => 'Europäische Rechnungseinheit (XBD)', 'XCD' => 'Ostkaribischer Dollar',
            'XDR' => 'Sonderziehungsrechte', 'XEU' => 'Europäische Währungseinheit (XEU)',
            'XFO' => 'Französischer Gold-Franc', 'XFU' => 'Französischer UIC-Franc',
            'XOF' => 'CFA Franc (West)', 'XPD' => 'Unze Palladium', 'XPF' => 'CFP Franc', 'XPT' => 'Unze Platin',
            'XRE' => 'RINET Funds', 'XTS' => 'Testwährung', 'XXX' => 'Unbekannte Währung', 'YDD' => 'Jemen-Dinar',
            'YER' => 'Jemen-Rial', 'YUD' => 'Jugoslawischer Dinar (1966-1990)', 'YUM' => 'Neuer Dinar',
            'YUN' => 'Jugoslawischer Dinar (konvertibel)', 'ZAL' => 'Südafrikanischer Rand (Finanz)',
            'ZAR' => 'Südafrikanischer Rand', 'ZMK' => 'Kwacha', 'ZRN' => 'Neuer Zaire', 'ZRZ' => 'Zaire', 'ZWD' => 'Simbabwe-Dollar',
            'ZWL' => 'Simbabwe-Dollar (2009)');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'nametocurrency', 'USD');
        $this->assertEquals("US-Dollar", $value);
    }

    /**
     * test for reading currencytoname from locale
     * expected array
     */
    public function testCurrencyToName()
    {
        $value = Cldr::getList('de_AT', 'currencytoname');
        $result = array('Andorranische Pesete' => 'ADP', 'UAE Dirham' => 'AED', 'Afghani (1927-2002)' => 'AFA',
            'Afghani' => 'AFN', 'Lek' => 'ALL', 'Dram' => 'AMD', 'Niederl. Antillen Gulden' => 'ANG',
            'Kwanza' => 'AOA', 'Angolanischer Kwanza (1977-1990)' => 'AOK', 'Neuer Kwanza' => 'AON',
            'Kwanza Reajustado' => 'AOR', 'Argentinischer Austral' => 'ARA', 'Argentinischer Peso (1983-1985)' => 'ARP',
            'Argentinischer Peso' => 'ARS', 'Österreichischer Schilling' => 'ATS', 'Australischer Dollar' => 'AUD',
            'Aruba Florin' => 'AWG', 'Aserbaidschan-Manat (1993-2006)' => 'AZM', 'Aserbaidschan-Manat' => 'AZN',
            'Bosnien und Herzegowina Dinar' => 'BAD', 'Konvertierbare Mark' => 'BAM', 'Barbados-Dollar' => 'BBD',
            'Taka' => 'BDT', 'Belgischer Franc (konvertibel)' => 'BEC', 'Belgischer Franc' => 'BEF',
            'Belgischer Finanz-Franc' => 'BEL', 'Lew (1962-1999)' => 'BGL', 'Lew' => 'BGN', 'Bahrain-Dinar' => 'BHD',
            'Burundi-Franc' => 'BIF', 'Bermuda-Dollar' => 'BMD', 'Brunei-Dollar' => 'BND', 'Boliviano' => 'BOB',
            'Bolivianischer Peso' => 'BOP', 'Mvdol' => 'BOV', 'Brasilianischer Cruzeiro Novo (1967-1986)' => 'BRB',
            'Brasilianischer Cruzado' => 'BRC', 'Brasilianischer Cruzeiro (1990-1993)' => 'BRE', 'Real' => 'BRL',
            'Brasilianischer Cruzado Novo' => 'BRN', 'Brasilianischer Cruzeiro' => 'BRR', 'Bahama-Dollar' => 'BSD',
            'Ngultrum' => 'BTN', 'Birmanischer Kyat' => 'BUK', 'Pula' => 'BWP', 'Belarus Rubel (alt)' => 'BYB',
            'Belarus Rubel (neu)' => 'BYR', 'Belize-Dollar' => 'BZD', 'Kanadischer Dollar' => 'CAD', 'Franc congolais' => 'CDF',
            'WIR-Euro' => 'CHE', 'Schweizer Franken' => 'CHF', 'WIR Franken' => 'CHW', 'Unidades de Fomento' => 'CLF',
            'Chilenischer Peso' => 'CLP', 'Renminbi Yuan' => 'CNY', 'Kolumbianischer Peso' => 'COP', 'Unidad de Valor Real' => 'COU',
            'Costa Rica Colon' => 'CRC', 'Alter Serbischer Dinar' => 'CSD', 'Tschechoslowakische Krone' => 'CSK',
            'Kubanischer Peso' => 'CUP', 'Kap Verde Escudo' => 'CVE', 'Zypern-Pfund' => 'CYP', 'Tschechische Krone' => 'CZK',
            'Mark der DDR' => 'DDM', 'Deutsche Mark' => 'DEM', 'Dschibuti-Franc' => 'DJF', 'Dänische Krone' => 'DKK',
            'Dominikanischer Peso' => 'DOP', 'Algerischer Dinar' => 'DZD', 'Ecuadorianischer Sucre' => 'ECS',
            'Verrechnungseinheit für EC' => 'ECV', 'Estnische Krone' => 'EEK', 'Ägyptisches Pfund' => 'EGP',
            'Ekwele' => 'GQE', 'Nakfa' => 'ERN', 'Spanische Peseta (A-Konten)' => 'ESA', 'Spanische Peseta (konvertibel)' => 'ESB',
            'Spanische Peseta' => 'ESP', 'Birr' => 'ETB', 'Euro' => 'EUR', 'Finnische Mark' => 'FIM',
            'Fidschi-Dollar' => 'FJD', 'Falkland-Pfund' => 'FKP', 'Französischer Franc' => 'FRF', 'Pfund Sterling' => 'GBP',
            'Georgischer Kupon Larit' => 'GEK', 'Georgischer Lari' => 'GEL', 'Cedi' => 'GHC', 'Gibraltar-Pfund' => 'GIP',
            'Dalasi' => 'GMD', 'Guinea-Franc' => 'GNF', 'Guineischer Syli' => 'GNS',
            'Griechische Drachme' => 'GRD', 'Quetzal' => 'GTQ', 'Portugiesisch Guinea Escudo' => 'GWE',
            'Guinea Bissau Peso' => 'GWP', 'Guyana-Dollar' => 'GYD', 'Hongkong-Dollar' => 'HKD', 'Lempira' => 'HNL',
            'Kroatischer Dinar' => 'HRD', 'Kuna' => 'HRK', 'Gourde' => 'HTG', 'Forint' => 'HUF', 'Rupiah' => 'IDR',
            'Irisches Pfund' => 'IEP', 'Israelisches Pfund' => 'ILP', 'Schekel' => 'ILS', 'Indische Rupie' => 'INR',
            'Irak Dinar' => 'IQD', 'Rial' => 'IRR', 'Isländische Krone' => 'ISK', 'Italienische Lira' => 'ITL',
            'Jamaika-Dollar' => 'JMD', 'Jordanischer Dinar' => 'JOD', 'Yen' => 'JPY', 'Kenia-Schilling' => 'KES',
            'Som' => 'KGS', 'Riel' => 'KHR', 'Komoren Franc' => 'KMF', 'Nordkoreanischer Won' => 'KPW',
            'Südkoreanischer Won' => 'KRW', 'Kuwait Dinar' => 'KWD', 'Kaiman-Dollar' => 'KYD', 'Tenge' => 'KZT',
            'Kip' => 'LAK', 'Libanesisches Pfund' => 'LBP', 'Sri Lanka Rupie' => 'LKR', 'Liberianischer Dollar' => 'LRD',
            'Loti' => 'LSL', 'Litauischer Litas' => 'LTL', 'Litauischer Talonas' => 'LTT',
            'Luxemburgischer Franc (konvertibel)' => 'LUC', 'Luxemburgischer Franc' => 'LUF', 'Luxemburgischer Finanz-Franc' => 'LUL',
            'Lettischer Lats' => 'LVL', 'Lettischer Rubel' => 'LVR', 'Libyscher Dinar' => 'LYD', 'Marokkanischer Dirham' => 'MAD',
            'Marokkanischer Franc' => 'MAF', 'Moldau Leu' => 'MDL', 'Madagaskar Ariary' => 'MGA', 'Madagaskar-Franc' => 'MGF',
            'Denar' => 'MKD', 'Malischer Franc' => 'MLF', 'Kyat' => 'MMK', 'Tugrik' => 'MNT', 'Pataca' => 'MOP',
            'Ouguiya' => 'MRO', 'Maltesische Lira' => 'MTL', 'Maltesisches Pfund' => 'MTP', 'Mauritius-Rupie' => 'MUR',
            'Rufiyaa' => 'MVR', 'Malawi Kwacha' => 'MWK', 'Mexikanischer Peso' => 'MXN', 'Mexikanischer Silber-Peso (1861-1992)' => 'MXP',
            'Mexican Unidad de Inversion (UDI)' => 'MXV', 'Malaysischer Ringgit' => 'MYR', 'Mosambikanischer Escudo' => 'MZE',
            'Alter Metical' => 'MZM', 'Metical' => 'MZN', 'Namibia-Dollar' => 'NAD', 'Naira' => 'NGN', 'Cordoba' => 'NIC',
            'Gold-Cordoba' => 'NIO', 'Holländischer Gulden' => 'NLG', 'Norwegische Krone' => 'NOK', 'Nepalesische Rupie' => 'NPR',
            'Neuseeland-Dollar' => 'NZD', 'Rial Omani' => 'OMR', 'Balboa' => 'PAB', 'Peruanischer Inti' => 'PEI',
            'Neuer Sol' => 'PEN', 'Sol' => 'PES', 'Kina' => 'PGK', 'Philippinischer Peso' => 'PHP', 'Pakistanische Rupie' => 'PKR',
            'Zloty' => 'PLN', 'Zloty (1950-1995)' => 'PLZ', 'Portugiesischer Escudo' => 'PTE', 'Guarani' => 'PYG',
            'Katar Riyal' => 'QAR', 'Rhodesischer Dollar' => 'RHD', 'Leu' => 'ROL', 'Rumänischer Leu' => 'RON',
            'Serbischer Dinar' => 'RSD', 'Russischer Rubel (neu)' => 'RUB', 'Russischer Rubel (alt)' => 'RUR',
            'Ruanda-Franc' => 'RWF', 'Saudi Riyal' => 'SAR', 'Salomonen-Dollar' => 'SBD', 'Seychellen-Rupie' => 'SCR',
            'Sudanesischer Dinar' => 'SDD', 'Sudanesisches Pfund' => 'SDG', 'Schwedische Krone' => 'SEK',
            'Singapur-Dollar' => 'SGD', 'St. Helena Pfund' => 'SHP', 'Tolar' => 'SIT', 'Slowakische Krone' => 'SKK',
            'Leone' => 'SLL', 'Somalia-Schilling' => 'SOS', 'Surinamischer Dollar' => 'SRD', 'Suriname Gulden' => 'SRG',
            'Dobra' => 'STD', 'Sowjetischer Rubel' => 'SUR', 'El Salvador Colon' => 'SVC', 'Syrisches Pfund' => 'SYP',
            'Lilangeni' => 'SZL', 'Baht' => 'THB', 'Tadschikistan Rubel' => 'TJR', 'Tadschikistan Somoni' => 'TJS',
            'Turkmenistan-Manat' => 'TMM', 'Tunesischer Dinar' => 'TND', 'Paʻanga' => 'TOP', 'Timor-Escudo' => 'TPE',
            'Alte Türkische Lira' => 'TRL', 'Türkische Lira' => 'TRY', 'Trinidad- und Tobago-Dollar' => 'TTD',
            'Neuer Taiwan-Dollar' => 'TWD', 'Tansania-Schilling' => 'TZS', 'Hryvnia' => 'UAH', 'Ukrainischer Karbovanetz' => 'UAK',
            'Uganda-Schilling (1966-1987)' => 'UGS', 'Uganda-Schilling' => 'UGX', 'US-Dollar' => 'USD',
            'US Dollar (Nächster Tag)' => 'USN', 'US Dollar (Gleicher Tag)' => 'USS', 'Uruguayischer Neuer Peso (1975-1993)' => 'UYP',
            'Uruguayischer Peso' => 'UYU', 'Usbekistan Sum' => 'UZS', 'Bolivar' => 'VEB', 'Dong' => 'VND', 'Vatu' => 'VUV',
            'Tala' => 'WST', 'CFA Franc (Äquatorial)' => 'XAF', 'Unze Silber' => 'XAG', 'Unze Gold' => 'XAU',
            'Europäische Rechnungseinheit' => 'XBA', 'Europäische Währungseinheit (XBB)' => 'XBB',
            'Europäische Rechnungseinheit (XBC)' => 'XBC', 'Europäische Rechnungseinheit (XBD)' => 'XBD',
            'Ostkaribischer Dollar' => 'XCD', 'Sonderziehungsrechte' => 'XDR', 'Europäische Währungseinheit (XEU)' => 'XEU',
            'Französischer Gold-Franc' => 'XFO', 'Französischer UIC-Franc' => 'XFU', 'CFA Franc (West)' => 'XOF',
            'Unze Palladium' => 'XPD', 'CFP Franc' => 'XPF', 'Unze Platin' => 'XPT', 'RINET Funds' => 'XRE',
            'Testwährung' => 'XTS', 'Unbekannte Währung' => 'XXX', 'Jemen-Dinar' => 'YDD', 'Jemen-Rial' => 'YER',
            'Jugoslawischer Dinar (1966-1990)' => 'YUD', 'Neuer Dinar' => 'YUM', 'Jugoslawischer Dinar (konvertibel)' => 'YUN',
            'Südafrikanischer Rand' => 'ZAR', 'Kwacha' => 'ZMK', 'Neuer Zaire' => 'ZRN', 'Zaire' => 'ZRZ', 'Simbabwe-Dollar' => 'ZWD',
            'Simbabwe-Dollar (2009)' => 'ZWL', 'Ghanaische Cedi' => 'GHS', 'Sudanesisches Pfund (alt)' => 'SDP', 'Bolívar Fuerte' => 'VEF',
            'Südafrikanischer Rand (Finanz)' => 'ZAL', 'UYU' => 'UYI', 'Neuer Turkmenistan-Manat' => 'TMT',
            'Kubanischer Peso (konvertibel)' => 'CUC');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'currencytoname', 'Unze Platin');
        $this->assertEquals("XPT", $value);
    }

    /**
     * test for reading currencysymbol from locale
     * expected array
     */
    public function testCurrencySymbol()
    {
        $value = Cldr::getList('de_AT', 'currencysymbol');
        $result = array(
            'AFN' => 'Af', 'ARS' => 'AR$', 'ATS' => 'öS',
            'AUD' => 'AU$', 'BAM' => 'KM', 'BBD' => 'Bds$', 'BDT' => 'Tk', 'BEF' => 'BF',
            'BHD' => 'BD', 'BIF' => 'FBu', 'BMD' => 'BD$', 'BOB' => 'Bs', 'BRL' => 'R$', 'BTN' => 'Nu.',
            'BZD' => 'BZ$', 'CAD' => 'CA$', 'CLP' => 'CL$', 'CNY' => 'CN¥',
            'COP' => 'CO$', 'CRC' => '₡', 'CVE' => 'CV$', 'CYP' => 'CY£', 'DEM' => 'DM', 'DJF' => 'Fdj',
            'DZD' => 'DA', 'ESP' => 'Pts', 'ETB' => 'Br', 'EUR' => '€', 'FJD' => 'FJ$', 'FRF' => '₣',
            'GBP' => '£', 'GNF' => 'FG', 'GYD' => 'GY$', 'HNL' => 'HNL',
            'HUF' => 'Ft', 'IDR' => 'Rp', 'IEP' => 'IR£', 'INR' => 'Rs',
            'ITL' => 'IT₤', 'JMD' => 'J$', 'JOD' => 'JD', 'JPY' => '¥', 'KES' => 'Ksh',
            'KMF' => 'CF', 'KWD' => 'KD', 'LBP' => 'LB£',
            'LKR' => 'SLRs', 'LSL' => 'LSL', 'LYD' => 'LD', 'MNT' => '₮', 'MRO' => 'UM',
            'MTL' => 'Lm', 'MYR' => 'RM', 'MZM' => 'Mt', 'MZN' => 'MTn',
            'NAD' => 'N$', 'NOK' => 'Nkr', 'NPR' => 'NPRs', 'NZD' => 'NZ$', 'PHP' => '₱',
            'PKR' => 'PKRs', 'PLN' => 'zł', 'QAR' => 'QR', 'SAR' => 'SR',
            'SBD' => 'SI$', 'SCR' => 'SRe', 'SGD' => 'S$', 'SKK' => 'Sk',
            'SRG' => 'Sf', 'STD' => 'Db', 'SYP' => 'SY£', 'SZL' => 'SZL', 'TOP' => 'T$', 'TRL' => 'TRL',
            'TTD' => 'TT$', 'TWD' => 'NT$', 'TZS' => 'TSh', 'UGX' => 'USh', 'USD' => '$', 'UYU' => '$U',
            'XCD' => 'EC$', 'YER' => 'YR', 'ZAR' => 'R', 'ZWD' => 'Z$', 'CUC' => 'CUC$',
            'ARM' => 'm$n', 'ARL' => '$L', 'ZRN' => 'NZ', 'ZRZ' => 'ZRZ', 'ZMK' => 'ZK', 'XPF' => 'CFPF',
            'XOF' => 'CFA', 'TMM' => 'TMM', 'SDD' => 'LSd', 'SEK' => 'Skr', 'SLL' => 'Le', 'SOS' => 'Ssh',
            'SRD' => 'SR$', 'TND' => 'DT', 'TRY' => 'TL', 'VEF' => 'Bs.F.', 'VUV' => 'VT', 'XAF' => 'FCFA',
            'WST' => 'WS$', 'PAB' => 'B/.', 'PEI' => 'I/.', 'PEN' => 'S/.', 'PGK' => 'PGK',
            'PTE' => 'Esc', 'RHD' => 'RH$', 'RON' => 'RON', 'RSD' => 'din.', 'LVL' => 'Ls', 'MMK' => 'MMK',
            'MOP' => 'MOP$', 'MUR' => 'MURs', 'MXN' => 'MX$', 'NIO' => 'C$', 'NLG' => 'fl',
            'CLE' => 'Eº', 'VND' => '₫', 'UAH' =>'₴', 'THB' => '฿', 'SVC' => 'SV₡',
            'SHP' => 'SH£', 'PYG' => '₲', 'NGN' => '₦', 'MTP' => 'MT£', 'LTL' => 'Lt', 'LRD' => 'L$',
            'LAK' => '₭', 'KYD' => 'KY$', 'KRW' => '₩', 'ISK' => 'Ikr', 'ILS' => '₪',
            'ILP' => 'I£', 'HTG' => 'HTG', 'HRK' => 'kn', 'HKD' => 'HK$', 'GTQ' => 'GTQ', 'GRD' => '₯',
            'GMD' => 'GMD', 'GIP' => 'GI£', 'GHS' => 'GH₵', 'GHC' => '₵', 'FKP' => 'FK£', 'FIM' => 'mk',
            'ERN' => 'Nfk', 'EEK' => 'Ekr', 'DOP' => 'RD$', 'DKK' => 'Dkr', 'CZK' => 'Kč',
            'CUP' => 'CU$', 'CDF' => 'CDF', 'BWP' => 'BWP', 'BSD' => 'BS$',
            'BOP' => '$b.', 'BND' => 'BN$', 'AZN' => 'man.',
            'AWG' => 'Afl.', 'ARA' => '₳', 'AOA' => 'Kz', 'ANG' => 'NAf.'
        );
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'currencysymbol', 'USD');
        $this->assertEquals("$", $value);
    }

    /**
     * test for reading question from locale
     * expected array
     */
    public function testQuestion()
    {
        $value = Cldr::getList('de_AT', 'question');
        $this->assertEquals(array("yes" => "ja:j", "no" => "nein:n"), $value);

        $value = Cldr::getContent('de_AT', 'question', 'yes');
        $this->assertEquals("ja:j", $value);
    }

    /**
     * test for reading currencyfraction from locale
     * expected array
     */
    public function testCurrencyFraction()
    {
        $value = Cldr::getList('de_AT', 'currencyfraction');
        $this->assertEquals(array('DEFAULT' => '2',
            'ADP' => '0', 'AFN' => '0', 'ALL' => '0', 'AMD' => '0', 'BHD' => '3', 'BIF' => '0', 'BYR' => '0',
            'CHF' => '2', 'CLF' => '0', 'CLP' => '0', 'COP' => '0', 'CRC' => '0', 'DJF' => '0', 'ESP' => '0',
            'GNF' => '0', 'GYD' => '0', 'HUF' => '0', 'IDR' => '0', 'IRR' => '0', 'ISK' => '0', 'IQD' => '0',
            'ITL' => '0', 'JOD' => '3', 'JPY' => '0', 'KMF' => '0', 'KPW' => '0', 'KRW' => '0', 'KWD' => '3',
            'LAK' => '0', 'LBP' => '0', 'LUF' => '0', 'LYD' => '3', 'MGA' => '0', 'MGF' => '0', 'MMK' => '0',
            'MNT' => '0', 'MRO' => '0', 'MUR' => '0', 'OMR' => '3', 'PKR' => '0', 'PYG' => '0', 'RSD' => '0',
            'RWF' => '0', 'SLL' => '0', 'SOS' => '0', 'STD' => '0', 'SYP' => '0', 'TMM' => '0', 'TND' => '3',
            'TRL' => '0', 'TZS' => '0', 'UGX' => '0', 'UZS' => '0', 'VND' => '0', 'VUV' => '0',
            'XAF' => '0', 'XOF' => '0', 'XPF' => '0', 'YER' => '0', 'ZMK' => '0', 'ZWD' => '0'), $value);

        $value = Cldr::getContent('de_AT', 'currencyfraction');
        $this->assertEquals("2", $value);

        $value = Cldr::getContent('de_AT', 'currencyfraction', 'BHD');
        $this->assertEquals("3", $value);
    }

    /**
     * test for reading currencyrounding from locale
     * expected array
     */
    public function testCurrencyRounding()
    {
        $value = Cldr::getList('de_AT', 'currencyrounding');
        $this->assertEquals(array('DEFAULT' => '0',
            'ADP' => '0', 'AFN' => '0', 'ALL' => '0', 'AMD' => '0', 'BHD' => '0', 'BIF' => '0', 'BYR' => '0',
            'CHF' => '5', 'CLF' => '0', 'CLP' => '0', 'COP' => '0', 'CRC' => '0', 'DJF' => '0', 'ESP' => '0',
            'GNF' => '0', 'GYD' => '0', 'HUF' => '0', 'IDR' => '0', 'IQD' => '0', 'IRR' => '0', 'ISK' => '0',
            'ITL' => '0', 'JOD' => '0', 'JPY' => '0', 'KMF' => '0', 'KPW' => '0', 'KRW' => '0', 'KWD' => '0',
            'LAK' => '0', 'LBP' => '0', 'LUF' => '0', 'LYD' => '0', 'MGA' => '0', 'MGF' => '0', 'MMK' => '0',
            'MNT' => '0', 'MRO' => '0', 'MUR' => '0', 'OMR' => '0', 'PKR' => '0', 'PYG' => '0', 'RSD' => '0',
            'RWF' => '0', 'SLL' => '0', 'SOS' => '0', 'STD' => '0', 'SYP' => '0', 'TMM' => '0', 'TND' => '0',
            'TRL' => '0', 'TZS' => '0', 'UGX' => '0', 'UZS' => '0', 'VND' => '0', 'VUV' => '0',
            'XAF' => '0', 'XOF' => '0', 'XPF' => '0', 'YER' => '0', 'ZMK' => '0', 'ZWD' => '0'), $value);

        $value = Cldr::getContent('de_AT', 'currencyrounding');
        $this->assertEquals("0", $value);

        $value = Cldr::getContent('de_AT', 'currencyrounding', 'BHD');
        $this->assertEquals("0", $value);
    }

    /**
     * test for reading currencytoregion from locale
     * expected array
     */
    public function testCurrencyToRegion()
    {
        $value = Cldr::getList('de_AT', 'currencytoregion');
        $result = array(   'AD' => 'EUR', 'AE' => 'AED', 'AF' => 'AFN', 'AG' => 'XCD', 'AI' => 'XCD',
            'AL' => 'ALL', 'AM' => 'AMD', 'AN' => 'ANG', 'AO' => 'AOA', 'AQ' => 'XXX', 'AR' => 'ARS',
            'AS' => 'USD', 'AT' => 'EUR', 'AU' => 'AUD', 'AW' => 'AWG', 'AX' => 'EUR', 'AZ' => 'AZN',
            'BA' => 'BAM', 'BB' => 'BBD', 'BD' => 'BDT', 'BE' => 'EUR', 'BF' => 'XOF', 'BG' => 'BGN',
            'BH' => 'BHD', 'BI' => 'BIF', 'BJ' => 'XOF', 'BM' => 'BMD', 'BN' => 'BND', 'BO' => 'BOB',
            'BR' => 'BRL', 'BS' => 'BSD', 'BT' => 'BTN', 'BV' => 'NOK', 'BW' => 'BWP', 'BY' => 'BYR',
            'BZ' => 'BZD', 'CA' => 'CAD', 'CC' => 'AUD', 'CD' => 'CDF', 'CF' => 'XAF', 'CG' => 'XAF',
            'CH' => 'CHF', 'CI' => 'XOF', 'CK' => 'NZD', 'CL' => 'CLP', 'CM' => 'XAF', 'CN' => 'CNY',
            'CO' => 'COP', 'CR' => 'CRC', 'CS' => 'CSD', 'CU' => 'CUC', 'CV' => 'CVE', 'CX' => 'AUD',
            'CY' => 'EUR', 'CZ' => 'CZK', 'DE' => 'EUR', 'DJ' => 'DJF', 'DK' => 'DKK', 'DM' => 'XCD',
            'DO' => 'DOP', 'DZ' => 'DZD', 'EC' => 'USD', 'EE' => 'EEK', 'EG' => 'EGP', 'EH' => 'MAD',
            'ER' => 'ERN', 'ES' => 'EUR', 'ET' => 'ETB', 'FI' => 'EUR', 'FJ' => 'FJD', 'FK' => 'FKP',
            'FM' => 'USD', 'FO' => 'DKK', 'FR' => 'EUR', 'GA' => 'XAF', 'GB' => 'GBP', 'GD' => 'XCD',
            'GE' => 'GEL', 'GF' => 'EUR', 'GG' => 'GBP', 'GH' => 'GHS', 'GI' => 'GIP', 'GL' => 'DKK',
            'GM' => 'GMD', 'GN' => 'GNF', 'GP' => 'EUR', 'GQ' => 'XAF', 'GR' => 'EUR', 'GS' => 'GBP',
            'GT' => 'GTQ', 'GU' => 'USD', 'GW' => 'XOF', 'GY' => 'GYD', 'HK' => 'HKD', 'HM' => 'AUD',
            'HN' => 'HNL', 'HR' => 'HRK', 'HT' => 'HTG', 'HU' => 'HUF', 'ID' => 'IDR', 'IE' => 'EUR',
            'IL' => 'ILS', 'IM' => 'GBP', 'IN' => 'INR', 'IO' => 'USD', 'IQ' => 'IQD', 'IR' => 'IRR',
            'IS' => 'ISK', 'IT' => 'EUR', 'JE' => 'GBP', 'JM' => 'JMD', 'JO' => 'JOD', 'JP' => 'JPY',
            'KE' => 'KES', 'KG' => 'KGS', 'KH' => 'KHR', 'KI' => 'AUD', 'KM' => 'KMF', 'KN' => 'XCD',
            'KP' => 'KPW', 'KR' => 'KRW', 'KW' => 'KWD', 'KY' => 'KYD', 'KZ' => 'KZT', 'LA' => 'LAK',
            'LB' => 'LBP', 'LC' => 'XCD', 'LI' => 'CHF', 'LK' => 'LKR', 'LR' => 'LRD', 'LS' => 'ZAR',
            'LT' => 'LTL', 'LU' => 'EUR', 'LV' => 'LVL', 'LY' => 'LYD', 'MA' => 'MAD', 'MC' => 'EUR',
            'MD' => 'MDL', 'ME' => 'EUR', 'MG' => 'MGA', 'MH' => 'USD', 'MK' => 'MKD', 'ML' => 'XOF',
            'MM' => 'MMK', 'MN' => 'MNT', 'MO' => 'MOP', 'MP' => 'USD', 'MQ' => 'EUR', 'MR' => 'MRO',
            'MS' => 'XCD', 'MT' => 'EUR', 'MU' => 'MUR', 'MV' => 'MVR', 'MW' => 'MWK', 'MX' => 'MXN',
            'MY' => 'MYR', 'MZ' => 'MZN', 'NA' => 'NAD', 'NC' => 'XPF', 'NE' => 'XOF', 'NF' => 'AUD',
            'NG' => 'NGN', 'NI' => 'NIO', 'NL' => 'EUR', 'NO' => 'NOK', 'NP' => 'NPR', 'NR' => 'AUD',
            'NU' => 'NZD', 'NZ' => 'NZD', 'OM' => 'OMR', 'PA' => 'PAB', 'PE' => 'PEN', 'PF' => 'XPF',
            'PG' => 'PGK', 'PH' => 'PHP', 'PK' => 'PKR', 'PL' => 'PLN', 'PM' => 'EUR', 'PN' => 'NZD',
            'PR' => 'USD', 'PS' => 'JOD', 'PT' => 'EUR', 'PW' => 'USD', 'PY' => 'PYG', 'QA' => 'QAR',
            'RE' => 'EUR', 'RO' => 'RON', 'RS' => 'RSD', 'RU' => 'RUB', 'RW' => 'RWF', 'SA' => 'SAR',
            'SB' => 'SBD', 'SC' => 'SCR', 'SD' => 'SDG', 'SE' => 'SEK', 'SG' => 'SGD', 'SH' => 'SHP',
            'SI' => 'EUR', 'SJ' => 'NOK', 'SK' => 'EUR', 'SL' => 'SLL', 'SM' => 'EUR', 'SN' => 'XOF',
            'SO' => 'SOS', 'SR' => 'SRD', 'ST' => 'STD', 'SV' => 'USD', 'SY' => 'SYP', 'SZ' => 'SZL',
            'TC' => 'USD', 'TD' => 'XAF', 'TF' => 'EUR', 'TG' => 'XOF', 'TH' => 'THB', 'TJ' => 'TJS',
            'TK' => 'NZD', 'TL' => 'USD', 'TM' => 'TMT', 'TN' => 'TND', 'TO' => 'TOP', 'TR' => 'TRY',
            'TT' => 'TTD', 'TV' => 'AUD', 'TW' => 'TWD', 'TZ' => 'TZS', 'UA' => 'UAH', 'UG' => 'UGX',
            'UM' => 'USD', 'US' => 'USD', 'UY' => 'UYU', 'UZ' => 'UZS', 'VA' => 'EUR', 'VC' => 'XCD',
            'VE' => 'VEF', 'VG' => 'USD', 'VI' => 'USD', 'VN' => 'VND', 'VU' => 'VUV', 'WF' => 'XPF',
            'WS' => 'WST', 'YE' => 'YER', 'YT' => 'EUR', 'ZA' => 'ZAR', 'ZM' => 'ZMK', 'ZW' => 'USD',
            'ZR' => 'ZRN', 'YU' => 'YUM', 'TP' => 'TPE', 'SU' => 'SUR', 'EU' => 'EUR', 'MF' => 'EUR',
            'DD' => 'DDM', 'BU' => 'BUK', 'BL' => 'EUR', 'ZZ' => 'XAG', 'YD' => 'YDD');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'currencytoregion', 'AT');
        $this->assertEquals("EUR", $value);
    }

    /**
     * test for reading regiontocurrency from locale
     * expected array
     */
    public function testRegionToCurrency()
    {
        $value = Cldr::getList('de_AT', 'regiontocurrency');
        $result = array(
            'EUR' => 'AD AT AX BE BL CY DE ES FI FR GF GP GR IE IT LU MC ME MF MQ MT NL PM PT EU RE SI SK SM TF VA YT',
            'AED' => 'AE', 'AFN' => 'AF', 'XCD' => 'AG AI DM GD KN LC MS VC', 'ALL' => 'AL', 'AMD' => 'AM',
            'ANG' => 'AN', 'AOA' => 'AO', 'XXX' => 'AQ', 'ARS' => 'AR', 'AWG' => 'AW', 'AZN' => 'AZ',
            'USD' => 'AS EC FM GU IO MH MP PR PW SV TC TL UM US VG VI ZW', 'AUD' => 'AU CC CX HM KI NF NR TV',
            'BAM' => 'BA', 'BBD' => 'BB', 'BDT' => 'BD', 'XOF' => 'BF BJ CI GW ML NE SN TG', 'BGN' => 'BG',
            'BHD' => 'BH', 'BIF' => 'BI', 'BMD' => 'BM', 'BND' => 'BN', 'BOB' => 'BO', 'BRL' => 'BR',
            'BSD' => 'BS', 'INR' => 'IN', 'NOK' => 'BV NO SJ', 'BWP' => 'BW', 'BYR' => 'BY', 'BZD' => 'BZ',
            'CAD' => 'CA', 'CDF' => 'CD', 'XAF' => 'CF CG CM GA GQ TD', 'CHF' => 'CH LI',
            'NZD' => 'CK NU NZ PN TK', 'CLP' => 'CL', 'CNY' => 'CN', 'COP' => 'CO', 'CRC' => 'CR',
            'CVE' => 'CV', 'CZK' => 'CZ', 'DJF' => 'DJ', 'DKK' => 'DK FO GL', 'DOP' => 'DO',
            'DZD' => 'DZ', 'EEK' => 'EE', 'EGP' => 'EG', 'MAD' => 'EH MA', 'ERN' => 'ER', 'ETB' => 'ET',
            'FJD' => 'FJ', 'FKP' => 'FK', 'GBP' => 'GB GG GS IM JE', 'GEL' => 'GE', 'GHS' => 'GH',
            'GIP' => 'GI', 'GMD' => 'GM', 'GNF' => 'GN', 'GTQ' => 'GT', 'GYD' => 'GY',
            'HKD' => 'HK', 'HNL' => 'HN', 'HRK' => 'HR', 'HTG' => 'HT', 'HUF' => 'HU', 'IDR' => 'ID',
            'ILS' => 'IL', 'IQD' => 'IQ', 'IRR' => 'IR', 'ISK' => 'IS', 'JMD' => 'JM', 'JOD' => 'JO PS',
            'JPY' => 'JP', 'KES' => 'KE', 'KGS' => 'KG', 'KHR' => 'KH', 'KMF' => 'KM', 'KPW' => 'KP',
            'KRW' => 'KR', 'KWD' => 'KW', 'KYD' => 'KY', 'KZT' => 'KZ', 'LAK' => 'LA', 'LBP' => 'LB',
            'LKR' => 'LK', 'LRD' => 'LR', 'ZAR' => 'LS ZA', 'LTL' => 'LT', 'LVL' => 'LV', 'LYD' => 'LY',
            'MDL' => 'MD', 'MGA' => 'MG', 'MKD' => 'MK', 'MMK' => 'MM', 'MNT' => 'MN', 'MOP' => 'MO',
            'MRO' => 'MR', 'MUR' => 'MU', 'MVR' => 'MV', 'MWK' => 'MW', 'MXN' => 'MX', 'MYR' => 'MY',
            'MZN' => 'MZ', 'XPF' => 'NC PF WF', 'NGN' => 'NG', 'NIO' => 'NI', 'NPR' => 'NP', 'OMR' => 'OM',
            'PAB' => 'PA', 'PEN' => 'PE', 'PGK' => 'PG', 'PHP' => 'PH', 'PKR' => 'PK', 'PLN' => 'PL',
            'PYG' => 'PY', 'QAR' => 'QA', 'RON' => 'RO', 'RSD' => 'RS', 'RUB' => 'RU', 'RWF' => 'RW',
            'SAR' => 'SA', 'SBD' => 'SB', 'SCR' => 'SC', 'SDG' => 'SD', 'SEK' => 'SE', 'SGD' => 'SG',
            'SHP' => 'SH', 'SLL' => 'SL', 'SOS' => 'SO', 'SRD' => 'SR', 'STD' => 'ST',
            'SYP' => 'SY', 'SZL' => 'SZ', 'THB' => 'TH', 'TJS' => 'TJ',
            'TND' => 'TN', 'TOP' => 'TO', 'TRY' => 'TR', 'TTD' => 'TT', 'TWD' => 'TW', 'TZS' => 'TZ',
            'UAH' => 'UA', 'UGX' => 'UG', 'UYU' => 'UY', 'UZS' => 'UZ', 'VEF' => 'VE', 'VND' => 'VN',
            'VUV' => 'VU', 'WST' => 'WS', 'YER' => 'YE', 'ZMK' => 'ZM', 'ZRN' => 'ZR',
            'YUM' => 'YU', 'TPE' => 'TP', 'SUR' => 'SU', 'DDM' => 'DD', 'CSD' => 'CS', 'BUK' => 'BU',
            'XAG' => 'ZZ', 'YDD' => 'YD', 'TMT' => 'TM', 'NAD' => 'NA', 'CUC' => 'CU',
            'BTN' => 'BT');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'regiontocurrency', 'EUR');
        $this->assertEquals("AD AT AX BE BL CY DE ES FI FR GF GP GR IE IT LU MC ME MF MQ MT NL PM PT EU RE SI SK SM TF VA YT", $value);
    }

    /**
     * test for reading regiontoterritory from locale
     * expected array
     */
    public function testRegionToTerritory()
    {
        $value = Cldr::getList('de_AT', 'regiontoterritory');
        $result = array('001' => '002 009 019 142 150',
            '011' => 'BF BJ CI CV GH GM GN GW LR ML MR NE NG SH SL SN TG', '013' => 'BZ CR GT HN MX NI PA SV',
            '014' => 'BI DJ ER ET KE KM MG MU MW MZ RE RW SC SO TZ UG YT ZM ZW',
            '142' => '030 035 143 145 034', '143' => 'TM TJ KG KZ UZ',
            '145' => 'AE AM AZ BH CY GE IL IQ JO KW LB OM PS QA SA SY TR YE',
            '015' => 'DZ EG EH LY MA SD TN EA IC', '018' => 'BW LS NA SZ ZA', '150' => '039 151 154 155 EU',
            '151' => 'BG BY CZ HU MD PL RO RU SK UA',
            '154' => 'GG IM JE AX DK EE FI FO GB IE IM IS LT LV NO SE SJ',
            '155' => 'AT BE CH DE FR LI LU MC NL', '017' => 'AO CD CF CG CM GA GQ ST TD',
            '019' => '005 013 021 029 003 419', '002' => '011 014 015 017 018', '021' => 'BM CA GL PM US',
            '029' => 'AG AI AN AW BB BL BS CU DM DO GD GP HT JM KN KY LC MF MQ MS PR TC TT VC VG VI',
            '003' => '013 021 029', '030' => 'CN HK JP KP KR MN MO TW',
            '035' => 'BN ID KH LA MM MY PH SG TH TL VN',
            '039' => 'AD AL BA ES GI GR HR IT ME MK MT RS PT SI SM VA', '419' => '005 013 029',
            '005' => 'AR BO BR CL CO EC FK GF GY PE PY SR UY VE', '053' => 'AU NF NZ',
            '054' => 'FJ NC PG SB VU', '057' => 'FM GU KI MH MP NR PW',
            '061' => 'AS CK NU PF PN TK TO TV WF WS', '034' => 'AF BD BT IN IR LK MV NP PK',
            '009' => '053 054 057 061 QO', 'QO' => 'AQ BV CC CX GS HM IO TF UM AC CP DG TA',
            'EU' => 'AT BE CY CZ DE DK EE ES FI FR GB GR HU IE IT LT LU LV MT NL PL PT SE SI SK BG RO');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'regiontoterritory', '143');
        $this->assertEquals("TM TJ KG KZ UZ", $value);
    }

    /**
     * test for reading territorytoregion from locale
     * expected array
     */
    public function testTerritoryToRegion()
    {
        $value = Cldr::getList('de_AT', 'territorytoregion');
        $result = array('002' => '001', '009' => '001', '019' => '001', '142' => '001', '150' => '001',
            'BF' => '011', 'BJ' => '011', 'CI' => '011', 'CV' => '011', 'GH' => '011', 'GM' => '011',
            'GN' => '011', 'GW' => '011', 'LR' => '011', 'ML' => '011', 'MR' => '011', 'NE' => '011',
            'NG' => '011', 'SH' => '011', 'SL' => '011', 'SN' => '011', 'TG' => '011', 'BZ' => '013',
            'CR' => '013', 'GT' => '013', 'HN' => '013', 'MX' => '013', 'NI' => '013', 'PA' => '013',
            'SV' => '013', 'BI' => '014', 'DJ' => '014', 'ER' => '014', 'ET' => '014', 'KE' => '014',
            'KM' => '014', 'MG' => '014', 'MU' => '014', 'MW' => '014', 'MZ' => '014', 'RE' => '014',
            'RW' => '014', 'SC' => '014', 'SO' => '014', 'TZ' => '014', 'UG' => '014', 'YT' => '014',
            'ZM' => '014', 'ZW' => '014', '030' => '142', '035' => '142', '143' => '142', '145' => '142',
            '034' => '142', 'TM' => '143', 'TJ' => '143', 'KG' => '143', 'IC' => '015',
            'KZ' => '143', 'UZ' => '143', 'AE' => '145', 'AM' => '145', 'AZ' => '145',
            'BH' => '145', 'CY' => '145 EU', 'GE' => '145', 'IL' => '145', 'IQ' => '145', 'JO' => '145',
            'KW' => '145', 'LB' => '145', 'OM' => '145', 'PS' => '145', 'QA' => '145', 'SA' => '145',
            'SY' => '145', 'TR' => '145', 'YE' => '145', 'DZ' => '015', 'EA' => '015',
            'EG' => '015', 'EH' => '015', 'LY' => '015', 'MA' => '015', 'SD' => '015', 'TN' => '015',
            '039' => '150', '151' => '150', '154' => '150', '155' => '150', 'EU' => '150', 'BG' => '151 EU',
            'BY' => '151', 'CZ' => '151 EU', 'HU' => '151 EU', 'MD' => '151', 'PL' => '151 EU',
            'RO' => '151 EU', 'RU' => '151', 'SK' => '151 EU', 'UA' => '151',
            'GG' => '154', 'IM' => '154 154', 'JE' => '154', 'AX' => '154', 'DK' => '154 EU',
            'EE' => '154 EU', 'FI' => '154 EU', 'FO' => '154', 'GB' => '154 EU', 'IE' => '154 EU',
            'IS' => '154', 'LT' => '154 EU', 'LV' => '154 EU', 'NO' => '154', 'SE' => '154 EU', 'SJ' => '154',
            'AT' => '155 EU', 'BE' => '155 EU', 'CH' => '155', 'DE' => '155 EU', 'DG' => 'QO',
            'FR' => '155 EU', 'LI' => '155', 'LU' => '155 EU', 'MC' => '155', 'NL' => '155 EU',
            'AO' => '017', 'CD' => '017', 'CF' => '017', 'CG' => '017', 'CM' => '017', 'CP' => 'QO',
            'GA' => '017', 'GQ' => '017', 'ST' => '017', 'TD' => '017', 'BW' => '018', 'LS' => '018',
            'NA' => '018', 'SZ' => '018', 'ZA' => '018', '005' => '019 419', '013' => '019 003 419',
            '021' => '019 003', '029' => '019 003 419', '003' => '019', '419' => '019', '011' => '002',
            '014' => '002', '015' => '002', '017' => '002', '018' => '002', 'BM' => '021', 'CA' => '021',
            'GL' => '021', 'PM' => '021', 'US' => '021', 'AG' => '029', 'AI' => '029', 'AN' => '029',
            'AW' => '029', 'BB' => '029', 'BS' => '029', 'CU' => '029', 'DM' => '029', 'DO' => '029',
            'GD' => '029', 'GP' => '029', 'HT' => '029', 'JM' => '029', 'KN' => '029', 'KY' => '029',
            'LC' => '029', 'MQ' => '029', 'MS' => '029', 'PR' => '029', 'TC' => '029', 'TT' => '029',
            'VC' => '029', 'VG' => '029', 'VI' => '029', 'CN' => '030', 'HK' => '030', 'JP' => '030',
            'KP' => '030', 'KR' => '030', 'MN' => '030', 'MO' => '030', 'TW' => '030', 'BN' => '035',
            'ID' => '035', 'KH' => '035', 'LA' => '035', 'MM' => '035', 'MY' => '035',
            'PH' => '035', 'SG' => '035', 'TA' => 'QO', 'TH' => '035', 'TL' => '035', 'VN' => '035',
            'AD' => '039', 'AL' => '039', 'BA' => '039', 'ES' => '039 EU', 'GI' => '039', 'GR' => '039 EU',
            'HR' => '039', 'IT' => '039 EU', 'ME' => '039', 'MK' => '039', 'MT' => '039 EU',
            'RS' => '039', 'PT' => '039 EU', 'SI' => '039 EU', 'SM' => '039', 'VA' => '039',
            'AR' => '005', 'BO' => '005', 'BR' => '005', 'CL' => '005', 'CO' => '005', 'EC' => '005',
            'FK' => '005', 'GF' => '005', 'GY' => '005', 'PE' => '005', 'PY' => '005', 'SR' => '005',
            'UY' => '005', 'VE' => '005', 'AU' => '053', 'NF' => '053', 'NZ' => '053', 'FJ' => '054',
            'NC' => '054', 'PG' => '054', 'SB' => '054', 'VU' => '054', 'FM' => '057', 'GU' => '057',
            'KI' => '057', 'MH' => '057', 'MP' => '057', 'NR' => '057', 'PW' => '057', 'AS' => '061',
            'CK' => '061', 'NU' => '061', 'PF' => '061', 'PN' => '061', 'TK' => '061', 'TO' => '061',
            'TV' => '061', 'WF' => '061', 'WS' => '061', 'AF' => '034', 'BD' => '034', 'BT' => '034',
            'IN' => '034', 'IR' => '034', 'LK' => '034', 'MV' => '034', 'NP' => '034', 'PK' => '034',
            '053' => '009', '054' => '009', '057' => '009', '061' => '009', 'QO' => '009', 'AQ' => 'QO',
            'BV' => 'QO', 'CC' => 'QO', 'CX' => 'QO', 'GS' => 'QO', 'HM' => 'QO', 'IO' => 'QO', 'TF' => 'QO',
            'UM' => 'QO', 'MF' => '029', 'BL' => '029', 'AC' => 'QO');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'territorytoregion', 'AT');
        $this->assertEquals("155 EU", $value);
    }

    /**
     * test for reading scripttolanguage from locale
     * expected array
     */
    public function testScriptToLanguage()
    {
        $value = Cldr::getList('de_AT', 'scripttolanguage');
        $result = array('aa' => 'Latn', 'ab' => 'Cyrl', 'abq' => 'Cyrl', 'ace' => 'Latn', 'ady' => 'Cyrl',
            'af' => 'Latn', 'aii' => 'Cyrl', 'ain' => 'Kana Latn', 'ak' => 'Latn', 'akk' => 'Xsux',
            'am' => 'Ethi', 'amo' => 'Latn', 'ar' => 'Arab', 'as' => 'Beng', 'ast' => 'Latn', 'av' => 'Cyrl',
            'awa' => 'Deva', 'ay' => 'Latn', 'az' => 'Arab Cyrl Latn', 'ba' => 'Cyrl', 'bal' => 'Arab Latn',
            'ban' => 'Latn', 'bbc' => 'Latn', 'be' => 'Cyrl', 'bem' => 'Latn', 'bfq' => 'Taml', 'bft' => 'Arab',
            'bfy' => 'Deva', 'bg' => 'Cyrl', 'bh' => 'Deva', 'bhb' => 'Deva', 'bho' => 'Deva', 'bi' => 'Latn',
            'bin' => 'Latn', 'bjj' => 'Deva', 'bku' => 'Latn', 'bm' => 'Latn', 'bn' => 'Beng', 'bo' => 'Tibt',
            'br' => 'Latn', 'bra' => 'Deva', 'bs' => 'Latn', 'btv' => 'Deva', 'buc' => 'Latn', 'bug' => 'Latn',
            'bxr' => 'Cyrl', 'bya' => 'Latn', 'byn' => 'Ethi', 'ca' => 'Latn', 'cch' => 'Latn', 'ccp' => 'Beng',
            'ce' => 'Cyrl', 'ceb' => 'Latn', 'ch' => 'Latn', 'chk' => 'Latn', 'chm' => 'Cyrl Latn',
            'chr' => 'Cher Latn', 'cja' => 'Cham', 'cjm' => 'Arab', 'cjs' => 'Cyrl', 'ckt' => 'Cyrl',
            'co' => 'Latn', 'cop' => 'Arab Copt Grek', 'cpe' => 'Latn', 'cr' => 'Cans Latn', 'crk' => 'Cans',
            'cs' => 'Latn', 'cu' => 'Glag', 'cv' => 'Cyrl', 'cwd' => 'Cans', 'cy' => 'Latn', 'da' => 'Latn',
            'dar' => 'Cyrl', 'de' => 'Latn', 'dgr' => 'Latn', 'dng' => 'Cyrl', 'doi' => 'Arab', 'dv' => 'Thaa',
            'dyu' => 'Latn', 'dz' => 'Tibt', 'ee' => 'Latn', 'efi' => 'Latn', 'el' => 'Grek', 'emk' => 'Nkoo',
            'en' => 'Latn', 'eo' => 'Latn', 'es' => 'Latn', 'et' => 'Latn', 'ett' => 'Ital Latn',
            'eu' => 'Latn', 'evn' => 'Cyrl', 'fa' => 'Arab', 'fan' => 'Latn', 'fi' => 'Latn', 'fil' => 'Latn',
            'fiu' => 'Latn', 'fj' => 'Latn', 'fo' => 'Latn', 'fon' => 'Latn', 'fr' => 'Latn', 'fur' => 'Latn',
            'fy' => 'Latn', 'ga' => 'Latn', 'gaa' => 'Latn', 'gag' => 'Latn', 'gbm' => 'Deva', 'gcr' => 'Latn',
            'gd' => 'Latn', 'gez' => 'Ethi', 'gil' => 'Latn', 'gl' => 'Latn', 'gld' => 'Cyrl', 'gn' => 'Latn',
            'gon' => 'Deva Telu', 'gor' => 'Latn', 'got' => 'Goth', 'grc' => 'Cprt Grek Linb', 'grt' => 'Beng',
            'gsw' => 'Latn', 'gu' => 'Gujr', 'gv' => 'Latn', 'gwi' => 'Latn', 'ha' => 'Arab Latn',
            'hai' => 'Latn', 'haw' => 'Latn', 'he' => 'Hebr', 'hi' => 'Deva', 'hil' => 'Latn', 'hmn' => 'Latn',
            'hne' => 'Deva', 'hnn' => 'Latn', 'ho' => 'Latn', 'hoc' => 'Deva', 'hoj' => 'Deva', 'hop' => 'Latn',
            'hr' => 'Latn', 'ht' => 'Latn', 'hu' => 'Latn', 'hy' => 'Armn', 'ia' => 'Latn', 'ibb' => 'Latn',
            'id' => 'Latn', 'ig' => 'Latn', 'ii' => 'Yiii', 'ik' => 'Latn', 'ilo' => 'Latn',
            'inh' => 'Cyrl', 'is' => 'Latn', 'it' => 'Latn', 'iu' => 'Cans', 'ja' => 'Jpan', 'jv' => 'Latn',
            'ka' => 'Geor', 'kaa' => 'Cyrl', 'kab' => 'Latn', 'kaj' => 'Latn', 'kam' => 'Latn', 'kbd' => 'Cyrl',
            'kca' => 'Cyrl', 'kcg' => 'Latn', 'kdt' => 'Thai', 'kfo' => 'Latn', 'kfr' => 'Deva',
            'kha' => 'Latn', 'khb' => 'Talu', 'kht' => 'Mymr', 'ki' => 'Latn', 'kj' => 'Latn', 'kjh' => 'Cyrl',
            'kk' => 'Cyrl', 'kl' => 'Latn', 'km' => 'Khmr', 'kmb' => 'Latn', 'kn' => 'Knda', 'ko' => 'Kore',
            'koi' => 'Cyrl', 'kok' => 'Deva', 'kos' => 'Latn', 'kpe' => 'Latn', 'kpv' => 'Cyrl',
            'kpy' => 'Cyrl', 'kr' => 'Latn', 'krc' => 'Cyrl', 'krl' => 'Cyrl Latn', 'kru' => 'Deva',
            'ks' => 'Arab Deva', 'ku' => 'Arab Cyrl Latn', 'kum' => 'Cyrl', 'kv' => 'Cyrl Latn', 'kw' => 'Latn',
            'ky' => 'Arab Cyrl', 'la' => 'Latn', 'lad' => 'Hebr', 'lah' => 'Arab', 'lb' => 'Latn',
            'lbe' => 'Cyrl', 'lcp' => 'Thai', 'lep' => 'Lepc', 'lez' => 'Cyrl', 'lg' => 'Latn', 'li' => 'Latn',
            'lif' => 'Deva Limb', 'lis' => 'Lisu', 'lmn' => 'Telu', 'ln' => 'Latn', 'lo' => 'Laoo',
            'lol' => 'Latn', 'lt' => 'Latn', 'lu' => 'Latn', 'lua' => 'Latn', 'luo' => 'Latn', 'lut' => 'Latn',
            'lv' => 'Latn', 'lwl' => 'Thai', 'mad' => 'Latn', 'mag' => 'Deva', 'mai' => 'Deva', 'mak' => 'Latn',
            'mdf' => 'Cyrl', 'mdh' => 'Latn', 'mdr' => 'Latn', 'men' => 'Latn', 'mfe' => 'Latn', 'mg' => 'Latn',
            'mh' => 'Latn', 'mi' => 'Latn', 'min' => 'Latn', 'mk' => 'Cyrl', 'ml' => 'Mlym',
            'mn' => 'Cyrl Mong', 'mnc' => 'Mong', 'mni' => 'Beng', 'mns' => 'Cyrl', 'mnw' => 'Mymr',
            'mos' => 'Latn', 'mr' => 'Deva', 'ms' => 'Latn', 'mt' => 'Latn',
            'mwr' => 'Deva', 'my' => 'Mymr', 'myv' => 'Cyrl', 'na' => 'Latn',
            'nap' => 'Latn', 'nb' => 'Latn', 'nbf' => 'Nkgb', 'nd' => 'Latn', 'ne' => 'Deva', 'new' => 'Deva',
            'ng' => 'Latn', 'niu' => 'Latn', 'nl' => 'Latn', 'nn' => 'Latn', 'no' => 'Latn', 'nog' => 'Cyrl',
            'nqo' => 'Nkoo', 'nr' => 'Latn', 'nso' => 'Latn', 'nv' => 'Latn', 'ny' => 'Latn', 'nym' => 'Latn',
            'nyn' => 'Latn', 'oc' => 'Latn', 'om' => 'Latn', 'or' => 'Orya', 'os' => 'Cyrl Latn',
            'osc' => 'Ital Latn', 'pa' => 'Guru', 'pag' => 'Latn', 'pam' => 'Latn', 'pap' => 'Latn',
            'pau' => 'Latn', 'peo' => 'Xpeo', 'phn' => 'Phnx', 'pi' => 'Deva Sinh Thai', 'pl' => 'Latn',
            'pon' => 'Latn', 'pra' => 'Khar', 'prd' => 'Arab', 'prg' => 'Latn', 'ps' => 'Arab', 'pt' => 'Latn',
            'qu' => 'Latn', 'rcf' => 'Latn', 'ril' => 'Beng', 'rm' => 'Latn', 'rn' => 'Latn', 'ro' => 'Latn',
            'rom' => 'Cyrl Latn', 'ru' => 'Cyrl', 'rw' => 'Latn', 'sa' => 'Deva Sinh', 'sah' => 'Cyrl',
            'sam' => 'Hebr Samr', 'sas' => 'Latn', 'sat' => 'Latn', 'scn' => 'Latn', 'sco' => 'Latn',
            'sd' => 'Arab Deva', 'se' => 'Latn', 'sel' => 'Cyrl', 'sg' => 'Latn', 'sga' => 'Latn Ogam',
            'shn' => 'Mymr', 'si' => 'Sinh', 'sid' => 'Latn', 'sk' => 'Latn', 'sl' => 'Latn', 'sm' => 'Latn',
            'sma' => 'Latn', 'smi' => 'Latn', 'smj' => 'Latn', 'smn' => 'Latn', 'sms' => 'Latn', 'sn' => 'Latn',
            'snk' => 'Latn', 'so' => 'Latn', 'son' => 'Latn', 'sq' => 'Latn', 'sr' => 'Cyrl Latn',
            'srn' => 'Latn', 'srr' => 'Latn', 'ss' => 'Latn', 'st' => 'Latn', 'su' => 'Latn', 'suk' => 'Latn',
            'sus' => 'Latn', 'sv' => 'Latn', 'sw' => 'Latn', 'swb' => 'Arab', 'syl' => 'Beng', 'syr' => 'Syrc',
            'ta' => 'Taml', 'tab' => 'Cyrl', 'tbw' => 'Latn', 'tcy' => 'Knda', 'tdd' => 'Tale', 'te' => 'Telu',
            'tem' => 'Latn', 'tet' => 'Latn', 'tg' => 'Arab Cyrl Latn', 'th' => 'Thai', 'ti' => 'Ethi',
            'tig' => 'Ethi', 'tiv' => 'Latn', 'tk' => 'Arab Cyrl Latn', 'tkl' => 'Latn', 'tl' => 'Latn',
            'tmh' => 'Latn', 'tn' => 'Latn', 'to' => 'Latn', 'tpi' => 'Latn', 'tr' => 'Latn', 'tru' => 'Latn',
            'ts' => 'Latn', 'tsg' => 'Latn', 'tt' => 'Cyrl', 'tts' => 'Thai', 'ttt' => 'Cyrl', 'tum' => 'Latn',
            'tut' => 'Cyrl', 'tvl' => 'Latn', 'ty' => 'Latn', 'tyv' => 'Cyrl',
            'tzm' => 'Latn Tfng', 'ude' => 'Cyrl', 'udm' => 'Cyrl', 'ug' => 'Arab', 'uga' => 'Ugar',
            'uk' => 'Cyrl', 'uli' => 'Latn', 'umb' => 'Latn', 'ur' => 'Arab', 'uz' => 'Arab Cyrl Latn',
            'vai' => 'Vaii', 've' => 'Latn', 'vi' => 'Latn', 'vo' => 'Latn', 'wa' => 'Latn', 'wal' => 'Ethi',
            'war' => 'Latn', 'wo' => 'Latn', 'xal' => 'Cyrl', 'xh' => 'Latn', 'xsr' => 'Deva',
            'xum' => 'Ital Latn', 'yao' => 'Latn', 'yap' => 'Latn', 'yi' => 'Hebr', 'yo' => 'Latn',
            'yrk' => 'Cyrl', 'za' => 'Latn', 'zh' => 'Hans Hant', 'zu' => 'Latn', 'zbl' => 'Blis', 'nds' => 'Latn',
            'hsb' => 'Latn', 'frs' => 'Latn', 'frr' => 'Latn', 'dsb' => 'Latn', 'kg' => 'Latn',
            'zza' => 'Arab', 'zun' => 'Latn', 'zen' => 'Tfng', 'zap' => 'Latn', 'was' => 'Latn',
            'vot' => 'Latn', 'unx' => 'Beng Deva', 'unr' => 'Beng Deva', 'tsi' => 'Latn', 'tog' => 'Latn',
            'tli' => 'Latn', 'ter' => 'Latn', 'sc' => 'Latn', 'sad' => 'Latn',
            'rup' => 'Latn', 'rar' => 'Latn', 'rap' => 'Latn', 'raj' => 'Latn', 'osa' => 'Latn',
            'oj' => 'Cans', 'nzi' => 'Latn', 'nyo' => 'Latn', 'nia' => 'Latn', 'mwl' => 'Latn',
            'mus' => 'Latn', 'moh' => 'Latn', 'mic' => 'Latn', 'mas' => 'Latn', 'man' => 'Latn',
            'lus' => 'Beng', 'lun' => 'Latn', 'lui' => 'Latn', 'loz' => 'Latn', 'lam' => 'Latn',
            'lab' => 'Lina', 'kut' => 'Latn', 'kac' => 'Mymr', 'jrb' => 'Hebr', 'jpr' => 'Hebr',
            'iba' => 'Latn', 'hz' => 'Latn', 'hup' => 'Latn', 'grb' => 'Latn', 'gba' => 'Arab',
            'gay' => 'Latn', 'ff' => 'Latn', 'fat' => 'Latn', 'ewo' => 'Latn', 'eka' => 'Latn',
            'dua' => 'Latn', 'din' => 'Latn', 'den' =>'Latn', 'del' => 'Latn', 'dak' => 'Latn',
            'csb' => 'Latn', 'crh' => 'Cyrl', 'chy' => 'Latn', 'chp' => 'Latn', 'cho' => 'Latn',
            'chn' => 'Latn', 'car' => 'Latn', 'cad' => 'Latn', 'bua' => 'Cyrl', 'bla' => 'Latn',
            'bik' => 'Latn', 'bej' => 'Arab', 'bas' => 'Latn', 'arw' => 'Latn', 'arp' => 'Latn',
            'arn' => 'Latn', 'anp' => 'Deva', 'an' => 'Latn', 'alt' => 'Cyrl', 'ale' => 'Latn',
            'ada' => 'Latn', 'ach' => 'Latn', 'ksh' => 'Latn', 'kri' => 'Latn'
        );
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'scripttolanguage', 'uk');
        $this->assertEquals("Cyrl", $value);
    }

    /**
     * test for reading languagetoscript from locale
     * expected array
     */
    public function testLanguageToScript()
    {
        $value = Cldr::getList('de_AT', 'languagetoscript');
        $result = array(
            'Latn' => 'aa ace ach ada af ain ak ale amo an arn arp arw ast ay az bal ban bas bbc bem bi bik bin bku bla bm br bs buc bug bya ca cad car cch ceb ch chk chm chn cho chp chr chy co cpe cr cs csb cy da dak de del den dgr din dsb dua dyu ee efi eka en eo es et ett eu ewo fan fat ff fi fil fiu fj fo fon fr frr frs fur fy ga gaa gag gay gcr gd gil gl gn gor grb gsw gv gwi ha hai haw hil hmn hnn ho hop hr hsb ht hu hup hz ia iba ibb id ig ik ilo is it jv kab kaj kam kcg kfo kg kha ki kj kl kmb kos kpe kr kri krl ksh ku kut kv kw la lam lb lg li ln lol loz lt lu lua lui lun luo lut lv mad mak man mas mdh mdr men mfe mg mh mi mic min moh mos ms mt mus mwl na nap nb nd nds ng nia niu nl nn no nr nso nv ny nym nyn nyo nzi oc om os osa osc pag pam pap pau pl pon prg pt qu raj rap rar rcf rm rn ro rom rup rw sad sas sat sc scn sco se sg sga sid sk sl sm sma smi smj smn sms sn snk so son sq sr srn srr ss st su suk sus sv sw tbw tem ter tet tg tiv tk tkl tl tli tmh tn to tog tpi tr tru ts tsg tsi tum tvl ty tzm uli umb uz ve vi vo vot wa war was wo xh xum yao yap yo za zap zu zun',
            'Cyrl' => 'ab abq ady aii alt av az ba be bg bua bxr ce chm cjs ckt crh cv dar dng evn gld inh kaa kbd kca kjh kk koi kpv kpy krc krl ku kum kv ky lbe lez mdf mk mn mns myv nog os rom ru sah sel sr tab tg tk tt ttt tut tyv ude udm uk uz xal yrk',
            'Kana' => 'ain', 'Xsux' => 'akk', 'Ethi' => 'am byn gez ti tig wal',
            'Arab' => 'ar az bal bej bft cjm cop doi fa gba ha ks ku ky lah prd ps sd swb tg tk ug ur uz zza',
            'Beng' => 'as bn ccp grt lus mni ril syl unr unx',
            'Deva' => 'anp awa bfy bh bhb bho bjj bra btv gbm gon hi hne hoc hoj kfr kok kru ks lif mag mai mr mwr ne new pi sa sd unr unx xsr',
            'Taml' => 'bfq ta', 'Tibt' => 'bo dz', 'Cher' => 'chr', 'Cham' => 'cja',
            'Copt' => 'cop', 'Grek' => 'cop el grc', 'Cans' => 'cr crk cwd iu oj', 'Glag' => 'cu', 'Thaa' => 'dv',
            'Ital' => 'ett osc xum', 'Telu' => 'gon lmn te', 'Goth' => 'got', 'Cprt' => 'grc', 'Linb' => 'grc',
            'Gujr' => 'gu', 'Hebr' => 'he jpr jrb lad sam yi', 'Armn' => 'hy', 'Yiii' => 'ii', 'Jpan' => 'ja',
            'Geor' => 'ka', 'Thai' => 'kdt lcp lwl pi th tts', 'Talu' => 'khb', 'Mymr' => 'kac kht mnw my shn',
            'Khmr' => 'km', 'Knda' => 'kn tcy', 'Laoo' => 'lo', 'Lepc' => 'lep', 'Limb' => 'lif',
            'Lisu' => 'lis', 'Mlym' => 'ml', 'Mong' => 'mn mnc', 'Nkoo' => 'emk nqo',
            'Orya' => 'or', 'Guru' => 'pa', 'Xpeo' => 'peo', 'Phnx' => 'phn', 'Sinh' => 'pi sa si',
            'Khar' => 'pra', 'Ogam' => 'sga', 'Syrc' => 'syr', 'Tale' => 'tdd',
            'Tfng' => 'tzm zen', 'Ugar' => 'uga', 'Vaii' => 'vai', 'Hans' => 'zh', 'Hant' => 'zh',
            'Blis' => 'zbl', 'Kore' => 'ko', 'Samr' => 'sam', 'Lina' => 'lab', 'Nkgb' => 'nbf');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'languagetoscript', 'Kana');
        $this->assertEquals("ain", $value);
    }

    /**
     * test for reading territorytolanguage from locale
     * expected array
     */
    public function testTerritoryToLanguage()
    {
        $value = Cldr::getList('de_AT', 'territorytolanguage');
        $result = array('aa' => 'DJ ET', 'ab' => 'GE', 'abr' => 'GH', 'ace' => 'ID', 'ady' => 'RU', 'af' => 'NA ZA',
            'ak' => 'GH', 'am' => 'ET', 'ar' => 'AE BH DJ DZ EG EH ER IL IQ JO KM KW LB LY MA MR OM PS QA SA SD SY TD TN YE',
            'as' => 'IN', 'ast' => 'ES', 'av' => 'RU', 'awa' => 'IN', 'ay' => 'BO', 'az' => 'AZ',
            'ba' => 'RU', 'bal' => 'IR PK', 'ban' => 'ID', 'bbc' => 'ID', 'bcl' => 'PH', 'be' => 'BY',
            'bem' => 'ZM', 'bew' => 'ID', 'bg' => 'BG', 'bgc' => 'IN', 'bhb' => 'IN', 'bhi' => 'IN',
            'bhk' => 'PH', 'bho' => 'IN MU NP', 'bi' => 'VU', 'bin' => 'NG', 'bjj' => 'IN', 'bjn' => 'ID MY',
            'bm' => 'ML', 'bn' => 'BD', 'bo' => 'CN', 'bqi' => 'IR', 'br' => 'FR', 'brh' => 'PK', 'brx' => 'IN', 'bs' => 'BA',
            'buc' => 'YT', 'bug' => 'ID', 'bya' => 'ID', 'ca' => 'AD', 'ce' => 'RU', 'ceb' => 'PH',
            'cgg' => 'UG', 'ch' => 'GU', 'chk' => 'FM', 'cs' => 'CZ', 'cv' => 'RU',
            'cy' => 'GB', 'da' => 'DK GL', 'dcc' => 'IN', 'de' => 'AT BE CH DE LI LU NA',
            'dhd' => 'IN', 'diq' => 'TR', 'dje' => 'NE', 'doi' => 'IN', 'dv' => 'MV', 'dyu' => 'BF',
            'dz' => 'BT', 'ee' => 'GH TG', 'efi' => 'NG', 'el' => 'CY GR', 'emk' => 'GN',
            'en' => 'AG AI AS AU BB BM BS BW BZ CA CC CK CM CX DG DM FJ FK FM GB GD GG GH GI GM GU GY HK IE IM IN IO JE JM KE KI KN KY LC LR LS MG MH MP MS MT MU MW NA NF NG NR NU NZ PG PH PK PN PR RW SB SC SG SH SL SZ TC TK TO TT TV TZ UG UM US VC VG VI VU WS ZA ZM ZW',
            'es' => 'AR BO CL CO CR CU DO EA EC ES GQ GT HN IC MX NI PA PE PH PR PY SV UY VE', 'et' => 'EE',
            'eu' => 'ES', 'fa' => 'AF IR', 'fan' => 'GQ', 'ff' => 'GN SN', 'ffm' => 'ML', 'fi' => 'FI', 'fil' => 'PH', 'fj' => 'FJ', 'fo' => 'FO', 'fon' => 'BJ',
            'fr' => 'BE BF BI BJ BL CA CD CF CG CH CI CM CP DJ DZ FR GA GF GN GP GQ HT KM LU MA MC MF MG ML MQ MU NC NE PF PM RE RW SC SN SY TD TG TN VU WF YT',
            'fud' => 'WF', 'fuv' => 'NG', 'fy' => 'NL', 'ga' => 'IE', 'gaa' => 'GH', 'gbm' => 'IN',
            'gcr' => 'GF', 'gd' => 'GB', 'gil' => 'KI', 'gl' => 'ES', 'glk' => 'IR', 'gn' => 'PY',
            'gno' => 'IN', 'gon' => 'IN', 'gor' => 'ID', 'gsw' => 'CH FR LI', 'gu' => 'IN', 'guz' => 'KE', 'ha' => 'NG',
            'haw' => 'US', 'haz' => 'AF', 'he' => 'IL', 'hi' => 'IN', 'hil' => 'PH', 'hne' => 'IN',
            'hno' => 'PK', 'ho' => 'PG', 'hoc' => 'IN', 'hr' => 'BA HR', 'ht' => 'HT', 'hu' => 'HU',
            'hy' => 'AM', 'ibb' => 'NG', 'id' => 'ID', 'ig' => 'NG', 'ii' => 'CN', 'ilo' => 'PH', 'inh' => 'RU',
            'is' => 'IS', 'it' => 'CH IT SM', 'iu' => 'GL', 'ja' => 'JP', 'jv' => 'ID', 'ka' => 'GE',
            'kab' => 'DZ', 'kam' => 'KE', 'kbd' => 'RU', 'kde' => 'TZ', 'kfy' => 'IN', 'kha' => 'IN', 'khn' => 'IN',
            'ki' => 'KE', 'kj' => 'NA', 'kk' => 'KZ', 'kl' => 'GL', 'kln' => 'KE', 'km' => 'KH', 'kmb' => 'AO',
            'kn' => 'IN', 'ko' => 'KP KR', 'koi' => 'RU', 'kok' => 'IN', 'kos' => 'FM',
            'kpv' => 'RU', 'krc' => 'RU', 'kri' => 'SL', 'kru' => 'IN', 'ks' => 'IN', 'ku' => 'IQ IR SY TR',
            'kum' => 'RU', 'kxm' => 'TH', 'ky' => 'KG', 'la' => 'VA', 'lah' => 'PK', 'lb' => 'LU',
            'lbe' => 'RU', 'lez' => 'RU', 'lg' => 'UG', 'ljp' => 'ID', 'lmn' => 'IN', 'ln' => 'CG',
            'lo' => 'LA', 'lrc' => 'IR', 'lt' => 'LT', 'lu' => 'CD', 'lua' => 'CD', 'luo' => 'KE',
            'luy' => 'KE', 'lv' => 'LV', 'mad' => 'ID', 'mag' => 'IN', 'mai' => 'IN NP', 'mak' => 'ID',
            'mdf' => 'RU', 'mdh' => 'PH', 'men' => 'SL', 'mer' => 'KE', 'mfa' => 'TH', 'mfe' => 'MU',
            'mg' => 'MG', 'mh' => 'MH', 'mi' => 'NZ', 'min' => 'ID', 'mk' => 'MK', 'ml' => 'IN', 'mn' => 'MN',
            'mni' => 'IN', 'mos' => 'BF', 'mr' => 'IN', 'ms' => 'BN MY SG', 'mt' => 'MT', 'mtr' => 'IN',
            'mup' => 'IN', 'my' => 'MM', 'myv' => 'RU', 'na' => 'NR', 'nap' => 'IT',
            'nb' => 'NO SJ', 'nd' => 'ZW', 'ndc' => 'MZ', 'nds' => 'DE', 'ne' => 'NP', 'ng' => 'NA', 'ngl' => 'MZ',
            'niu' => 'NU', 'nl' => 'AN AW BE NL SR', 'nn' => 'NO', 'nod' => 'TH', 'noe' => 'IN', 'nr' => 'ZA', 'nso' => 'ZA',
            'ny' => 'MW', 'nym' => 'TZ', 'nyn' => 'UG', 'om' => 'ET', 'or' => 'IN', 'os' => 'GE', 'pa' => 'IN PK',
            'pag' => 'PH', 'pam' => 'PH', 'pap' => 'AN', 'pau' => 'PW', 'pl' => 'PL', 'pon' => 'FM',
            'ps' => 'AF', 'pt' => 'AO BR CV GW MZ PT ST TL', 'qu' => 'BO PE', 'rcf' => 'RE', 'rej' => 'ID',
            'rif' => 'MA', 'rjb' => 'IN', 'rm' => 'CH', 'rmt' => 'IR', 'rn' => 'BI', 'ro' => 'MD RO',
            'ru' => 'BY KG KZ RU UA', 'rw' => 'RW', 'sa' => 'IN', 'sah' => 'RU', 'sas' => 'ID', 'sat' => 'IN',
            'sck' => 'IN', 'scn' => 'IT', 'sco' => 'GB', 'sd' => 'IN PK', 'se' => 'NO', 'seh' => 'MZ', 'sg' => 'CF',
            'shi' => 'MA', 'shn' => 'MM', 'si' => 'LK', 'sid' => 'ET', 'sk' => 'SK', 'sl' => 'SI', 'sm' => 'AS WS',
            'sn' => 'ZW', 'so' => 'SO', 'sou' => 'TH', 'sq' => 'AL MK', 'sr' => 'BA ME RS', 'srn' => 'SR',
            'srr' => 'SN', 'ss' => 'SZ ZA', 'st' => 'LS ZA', 'su' => 'ID', 'suk' => 'TZ', 'sv' => 'AX FI SE',
            'sw' => 'KE TZ UG', 'swb' => 'YT', 'swv' => 'IN', 'syl' => 'BD', 'ta' => 'LK SG', 'tcy' => 'IN',
            'te' => 'IN', 'tem' => 'SL', 'tet' => 'TL', 'tg' => 'TJ', 'th' => 'TH', 'ti' => 'ER', 'tiv' => 'NG',
            'tk' => 'TM', 'tkl' => 'TK', 'tl' => 'PH US', 'tn' => 'BW ZA', 'to' => 'TO', 'tpi' => 'PG',
            'tr' => 'CY TR', 'ts' => 'ZA', 'tsg' => 'PH', 'tt' => 'RU', 'tts' => 'TH', 'tvl' => 'TV',
            'ty' => 'PF', 'tyv' => 'RU', 'tzm' => 'MA', 'udm' => 'RU', 'ug' => 'CN', 'uk' => 'UA',
            'uli' => 'FM', 'umb' => 'AO', 'und' => 'AQ BV GS HM', 'ur' => 'PK', 'uz' => 'UZ',
            've' => 'ZA', 'vi' => 'VN', 'vmw' => 'MZ', 'wal' => 'ET', 'war' => 'PH', 'wbq' => 'IN',
            'wbr' => 'IN', 'wls' => 'WF', 'wo' => 'SN', 'wtm' => 'IN', 'xh' => 'ZA', 'xnr' => 'IN',
            'xog' => 'UG', 'yap' => 'FM', 'yo' => 'NG', 'za' => 'CN', 'zdj' => 'KM', 'zh' => 'CN HK MO SG TW', 'zu' => 'ZA',
            'oc' => 'FR', 'kg' => 'CD', 'unr' => 'IN', 'tum' => 'MW', 'tig' => 'ER', 'teo' => 'UG',
            'sus' => 'GN', 'skr' => 'PK', 'mwr' => 'IN', 'laj' => 'UG', 'kea' => 'CV', 'gag' => 'MD',
            'fuq' => 'NE', 'crs' => 'SC', 'bci' => 'CI');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'territorytolanguage', 'uk');
        $this->assertEquals("UA", $value);
    }

    /**
     * test for reading languagetoterritory from locale
     * expected array
     */
    public function testLanguageToTerritory()
    {
        $value = Cldr::getList('de_AT', 'languagetoterritory');
        $result = array('DJ' => 'aa ar fr', 'GE' => 'ab ka os', 'GH' => 'abr ak ee en gaa',
            'ID' => 'ace ban bbc bew bjn bug bya gor id jv ljp mad mak min rej sas su',
            'RU' => 'ady av ba ce cv inh kbd koi kpv krc kum lbe lez mdf myv ru sah tt tyv udm',
            'ZA' => 'af en nr nso ss st tn ts ve xh zu', 'ET' => 'aa am om sid wal', 'AE' => 'ar', 'BH' => 'ar',
            'DZ' => 'ar fr kab', 'EG' => 'ar', 'EH' => 'ar', 'ER' => 'ar ti tig', 'IL' => 'ar he', 'IQ' => 'ar ku',
            'JO' => 'ar', 'KM' => 'ar fr zdj', 'KW' => 'ar', 'LB' => 'ar', 'LY' => 'ar',
            'MA' => 'ar fr rif shi tzm', 'MR' => 'ar', 'OM' => 'ar', 'PS' => 'ar', 'QA' => 'ar', 'SA' => 'ar',
            'SD' => 'ar', 'SY' => 'ar fr ku', 'TD' => 'ar fr', 'TN' => 'ar fr', 'YE' => 'ar',
            'IN' => 'as awa bgc bhb bhi bho bjj brx dcc dhd doi en gbm gno gon gu hi hne hoc kfy kha khn kn kok kru ks lmn mag mai ml mni mr mtr mup mwr noe or pa rjb sa sat sck sd swv tcy te unr wbq wbr wtm xnr',
            'ES' => 'ast es eu gl', 'FR' => 'br fr gsw oc', 'BO' => 'ay es qu', 'AZ' => 'az',
            'PK' => 'bal brh en hno lah pa sd skr ur', 'PH' => 'bcl bhk ceb en es fil hil ilo mdh pag pam tl tsg war',
            'BY' => 'be ru', 'ZM' => 'bem en', 'BG' => 'bg', 'MU' => 'bho en fr mfe', 'NP' => 'bho mai ne',
            'VU' => 'bi en fr', 'NG' => 'bin efi en fuv ha ibb ig tiv yo', 'ML' => 'bm ffm fr', 'BD' => 'bn syl',
            'CN' => 'bo ii ug za zh', 'IR' => 'bal bqi fa glk ku lrc rmt', 'BA' => 'bs hr sr', 'YT' => 'buc fr swb',
            'AD' => 'ca', 'UG' => 'cgg en laj lg nyn sw teo xog', 'GU' => 'ch en', 'FM' => 'chk en kos pon uli yap',
            'CA' => 'en fr', 'CZ' => 'cs', 'GB' => 'cy en gd sco', 'DK' => 'da', 'GL' => 'da iu kl',
            'AT' => 'de', 'BE' => 'de fr nl', 'CH' => 'de fr gsw it rm', 'DE' => 'de nds', 'LI' => 'de gsw',
            'LU' => 'de fr lb', 'TR' => 'diq ku tr', 'NE' => 'dje fr fuq', 'MV' => 'dv', 'BF' => 'dyu fr mos',
            'BT' => 'dz', 'CY' => 'el tr', 'GR' => 'el', 'GN' => 'emk ff fr sus', 'AG' => 'en', 'AI' => 'en',
            'AS' => 'en sm', 'AU' => 'en', 'BB' => 'en', 'BM' => 'en', 'BS' => 'en', 'BW' => 'en tn',
            'BZ' => 'en', 'CC' => 'en', 'CK' => 'en', 'CM' => 'en fr', 'CX' => 'en', 'DM' => 'en',
            'FJ' => 'en fj', 'FK' => 'en', 'GD' => 'en', 'GG' => 'en', 'GI' => 'en', 'GM' => 'en', 'GY' => 'en',
            'HK' => 'en zh', 'HN' => 'es', 'IE' => 'en ga', 'IM' => 'en', 'JE' => 'en', 'JM' => 'en',
            'KE' => 'en guz kam ki kln luo luy mer sw', 'KI' => 'en gil', 'KN' => 'en', 'KY' => 'en',
            'LC' => 'en', 'LR' => 'en', 'LS' => 'en st', 'MH' => 'en mh', 'MP' => 'en', 'MS' => 'en',
            'MT' => 'en mt', 'MW' => 'en ny tum', 'NA' => 'af de en kj ng', 'NF' => 'en', 'NR' => 'en na',
            'NU' => 'en niu', 'NZ' => 'en mi', 'PG' => 'en ho tpi', 'PN' => 'en', 'PR' => 'en es',
            'RW' => 'en fr rw', 'SB' => 'en', 'SC' => 'crs en fr', 'SG' => 'en ms ta zh', 'SH' => 'en',
            'SL' => 'en kri men tem', 'SZ' => 'en ss', 'TC' => 'en', 'TK' => 'en tkl', 'TO' => 'en to',
            'TT' => 'en', 'TV' => 'en tvl', 'TZ' => 'en kde nym suk sw', 'UM' => 'en', 'US' => 'en haw tl',
            'VC' => 'en', 'VG' => 'en', 'VI' => 'en', 'WS' => 'en sm', 'ZW' => 'en nd sn', 'AR' => 'es',
            'CL' => 'es', 'CO' => 'es', 'CR' => 'es', 'CU' => 'es', 'DO' => 'es', 'EC' => 'es',
            'GQ' => 'es fan fr', 'GT' => 'es', 'MX' => 'es', 'NI' => 'es', 'PA' => 'es', 'PE' => 'es qu',
            'PY' => 'es gn', 'SV' => 'es', 'UY' => 'es', 'VE' => 'es', 'EE' => 'et', 'AF' => 'fa haz ps',
            'FI' => 'fi sv', 'FO' => 'fo', 'BJ' => 'fon fr', 'BI' => 'fr rn', 'CD' => 'fr kg lu lua',
            'CF' => 'fr sg', 'CG' => 'fr ln', 'CI' => 'bci fr', 'GA' => 'fr', 'GF' => 'fr gcr', 'GP' => 'fr',
            'HT' => 'fr ht', 'MC' => 'fr', 'MG' => 'en fr mg', 'MQ' => 'fr', 'NC' => 'fr', 'PF' => 'fr ty',
            'PM' => 'fr', 'RE' => 'fr rcf', 'SN' => 'ff fr srr wo', 'TG' => 'ee fr', 'WF' => 'fr fud wls',
            'NL' => 'fy nl', 'HR' => 'hr', 'HU' => 'hu', 'AM' => 'hy', 'IS' => 'is', 'IT' => 'it nap scn',
            'SM' => 'it', 'JP' => 'ja', 'KZ' => 'kk ru', 'KH' => 'km', 'AO' => 'kmb pt umb', 'KP' => 'ko',
            'KR' => 'ko', 'TH' => 'kxm mfa nod sou th tts', 'KG' => 'ky ru', 'VA' => 'la', 'LA' => 'lo',
            'LT' => 'lt', 'LV' => 'lv', 'MK' => 'mk sq', 'MN' => 'mn', 'BN' => 'ms', 'MY' => 'bjn ms',
            'MM' => 'my shn', 'NO' => 'nb nn se', 'SJ' => 'nb', 'MZ' => 'ndc ngl pt seh vmw', 'AN' => 'nl pap',
            'AW' => 'nl', 'SR' => 'nl srn', 'PW' => 'pau', 'PL' => 'pl', 'BR' => 'pt', 'CV' => 'kea pt',
            'GW' => 'pt', 'PT' => 'pt', 'ST' => 'pt', 'TL' => 'pt tet', 'MD' => 'gag ro', 'RO' => 'ro',
            'LK' => 'si ta', 'SK' => 'sk', 'SI' => 'sl', 'SO' => 'so', 'AL' => 'sq', 'ME' => 'sr', 'RS' => 'sr',
            'AX' => 'sv', 'SE' => 'sv', 'TJ' => 'tg', 'TM' => 'tk', 'UA' => 'ru uk', 'AQ' => 'und', 'BV' => 'und',
            'GS' => 'und', 'HM' => 'und', 'IO' => 'en', 'UZ' => 'uz', 'VN' => 'vi',
            'MO' => 'zh', 'TW' => 'zh', 'BL' => 'fr', 'MF' => 'fr', 'CP' => 'fr', 'DG' => 'en', 'EA' => 'es',
            'IC' => 'es');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'languagetoterritory', 'GQ');
        $this->assertEquals("es fan fr", $value);
    }

    /**
     * test for reading timezonetowindows from locale
     * expected array
     */
    public function testTimezoneToWindows()
    {
        $value = Cldr::getList('de_AT', 'timezonetowindows');
        $result = array('Dateline Standard Time' => 'Etc/GMT+12', 'Samoa Standard Time' => 'Pacific/Apia',
            'Hawaiian Standard Time' => 'Pacific/Honolulu', 'Alaskan Standard Time' => 'America/Anchorage', 'Pacific Standard Time' => 'America/Los_Angeles',
            'Pacific Standard Time (Mexico)' => 'America/Santa_Isabel', 'US Mountain Standard Time' => 'America/Phoenix',
            'Mountain Standard Time' => 'America/Denver', 'Mountain Standard Time (Mexico)' => 'America/Chihuahua',
            'Mexico Standard Time 2' => 'America/Chihuahua', 'Central America Standard Time' => 'America/Guatemala',
            'Canada Central Standard Time' => 'America/Regina', 'Central Standard Time (Mexico)' => 'America/Mexico_City',
            'Mexico Standard Time' => 'America/Mexico_City', 'Central Standard Time' => 'America/Chicago', 'US Eastern Standard Time' => 'America/Indianapolis',
            'SA Pacific Standard Time' => 'America/Bogota', 'Eastern Standard Time' => 'America/New_York', 'Venezuela Standard Time' => 'America/Caracas',
            'Pacific SA Standard Time' => 'America/Santiago', 'Atlantic Standard Time' => 'America/Halifax', 'Central Brazilian Standard Time' => 'America/Cuiaba',
            'Newfoundland Standard Time' => 'America/St_Johns', 'Greenland Standard Time' => 'America/Godthab',
            'E. South America Standard Time' => 'America/Sao_Paulo', 'Montevideo Standard Time' => 'America/Montevideo', 'Mid-Atlantic Standard Time' => 'Etc/GMT+2',
            'Cape Verde Standard Time' => 'Atlantic/Cape_Verde', 'Azores Standard Time' => 'Atlantic/Azores', 'Greenwich Standard Time' => 'Atlantic/Reykjavik',
            'GMT Standard Time' => 'Europe/London', 'W. Central Africa Standard Time' => 'Africa/Lagos', 'W. Europe Standard Time' => 'Europe/Berlin',
            'Romance Standard Time' => 'Europe/Paris', 'Central European Standard Time' => 'Europe/Warsaw', 'Central Europe Standard Time' => 'Europe/Budapest',
            'South Africa Standard Time' => 'Africa/Johannesburg', 'Israel Standard Time' => 'Asia/Jerusalem', 'GTB Standard Time' => 'Europe/Istanbul',
            'FLE Standard Time' => 'Europe/Kiev', 'Egypt Standard Time' => 'Africa/Cairo', 'E. Europe Standard Time' => 'Europe/Minsk',
            'Jordan Standard Time' => 'Asia/Amman', 'Middle East Standard Time' => 'Asia/Beirut', 'Namibia Standard Time' => 'Africa/Windhoek',
            'E. Africa Standard Time' => 'Africa/Nairobi', 'Azerbaijan Standard Time' => 'Asia/Baku', 'Arab Standard Time' => 'Asia/Riyadh',
            'Georgian Standard Time' => 'Asia/Tbilisi', 'Russian Standard Time' => 'Europe/Moscow', 'Arabic Standard Time' => 'Asia/Baghdad',
            'Iran Standard Time' => 'Asia/Tehran', 'Arabian Standard Time' => 'Asia/Dubai', 'Caucasus Standard Time' => 'Asia/Yerevan', 'Afghanistan Standard Time' => 'Asia/Kabul',
            'West Asia Standard Time' => 'Asia/Tashkent', 'Ekaterinburg Standard Time' => 'Asia/Yekaterinburg', 'India Standard Time' => 'Asia/Calcutta',
            'Nepal Standard Time' => 'Asia/Katmandu', 'Sri Lanka Standard Time' => 'Asia/Colombo', 'Central Asia Standard Time' => 'Asia/Almaty',
            'N. Central Asia Standard Time' => 'Asia/Novosibirsk', 'Myanmar Standard Time' => 'Asia/Rangoon', 'SE Asia Standard Time' => 'Asia/Bangkok',
            'North Asia Standard Time' => 'Asia/Krasnoyarsk', 'W. Australia Standard Time' => 'Australia/Perth', 'Taipei Standard Time' => 'Asia/Taipei',
            'Singapore Standard Time' => 'Asia/Singapore', 'China Standard Time' => 'Asia/Shanghai', 'North Asia East Standard Time' => 'Asia/Ulaanbaatar',
            'Tokyo Standard Time' => 'Asia/Tokyo', 'Korea Standard Time' => 'Asia/Seoul', 'Yakutsk Standard Time' => 'Asia/Yakutsk', 'AUS Central Standard Time' => 'Australia/Darwin',
            'Cen. Australia Standard Time' => 'Australia/Adelaide', 'E. Australia Standard Time' => 'Australia/Brisbane',
            'Vladivostok Standard Time' => 'Asia/Vladivostok', 'Tasmania Standard Time' => 'Australia/Hobart', 'AUS Eastern Standard Time' => 'Australia/Sydney',
            'Central Pacific Standard Time' => 'Pacific/Guadalcanal', 'Fiji Standard Time' => 'Pacific/Fiji', 'New Zealand Standard Time' => 'Pacific/Auckland',
            'Tonga Standard Time' => 'Pacific/Tongatapu', 'West Pacific Standard Time' => 'Pacific/Port_Moresby',
            'US Eastern Standard Time' => 'Etc/GMT+5', 'SA Eastern Standard Time' => 'America/Cayenne',
            'SA Western Standard Time' => 'America/La_Paz', 'North Asia East Standard Time' => 'Asia/Irkutsk',
            'Argentina Standard Time' => 'America/Buenos_Aires', 'Armenian Standard Time' => 'Asia/Yerevan',
            'Pakistan Standard Time' => 'Asia/Karachi', 'Morocco Standard Time' => 'Africa/Casablanca',
            'Mauritius Standard Time' => 'Indian/Mauritius', 'Bangladesh Standard Time' => 'Asia/Dhaka',
            'Kamchatka Standard Time' => 'Asia/Kamchatka', 'Paraguay Standard Time' => 'America/Asuncion',
            'Syria Standard Time' => 'Asia/Damascus', 'UTC' => 'Etc/GMT', 'UTC+12' => 'Etc/GMT-12', 'UTC-02' => 'Etc/GMT+2', 'UTC-11' => 'Etc/GMT+11',
            'Ulaanbaatar Standard Time' => 'Asia/Ulaanbaatar');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'timezonetowindows', 'Fiji Standard Time');
        $this->assertEquals("Pacific/Fiji", $value);
    }

    /**
     * test for reading windowstotimezone from locale
     * expected array
     */
    public function testWindowsToTimezone()
    {
        $value = Cldr::getList('de_AT', 'windowstotimezone');
        $result = array('Pacific/Apia' => 'Samoa Standard Time', 'Pacific/Honolulu' => 'Hawaiian Standard Time',
            'America/Anchorage' => 'Alaskan Standard Time', 'America/Los_Angeles' => 'Pacific Standard Time', 'America/Santa_Isabel' => 'Pacific Standard Time (Mexico)',
            'America/Phoenix' => 'US Mountain Standard Time', 'America/Denver' => 'Mountain Standard Time', 'America/Chihuahua' => 'Mexico Standard Time 2',
            'America/Guatemala' => 'Central America Standard Time', 'America/Regina' => 'Canada Central Standard Time', 'America/Mexico_City' => 'Central Standard Time (Mexico)',
            'America/Chicago' => 'Central Standard Time', 'America/Bogota' => 'SA Pacific Standard Time',
            'America/New_York' => 'Eastern Standard Time', 'America/Caracas' => 'Venezuela Standard Time', 'America/Santiago' => 'Pacific SA Standard Time',
            'America/Halifax' => 'Atlantic Standard Time', 'America/Cuiaba' => 'Central Brazilian Standard Time', 'America/St_Johns' => 'Newfoundland Standard Time',
            'America/Buenos_Aires' => 'Argentina Standard Time', 'America/Godthab' => 'Greenland Standard Time', 'America/Sao_Paulo' => 'E. South America Standard Time',
            'America/Montevideo' => 'Montevideo Standard Time', 'Atlantic/Cape_Verde' => 'Cape Verde Standard Time',
            'Atlantic/Azores' => 'Azores Standard Time', 'Africa/Casablanca' => 'Morocco Standard Time', 'Europe/London' => 'GMT Standard Time',
            'Africa/Lagos' => 'W. Central Africa Standard Time', 'Europe/Berlin' => 'W. Europe Standard Time', 'Europe/Paris' => 'Romance Standard Time',
            'Europe/Warsaw' => 'Central European Standard Time', 'Africa/Johannesburg' => 'South Africa Standard Time',
            'Asia/Jerusalem' => 'Israel Standard Time', 'Europe/Istanbul' => 'GTB Standard Time',
            'Africa/Cairo' => 'Egypt Standard Time', 'Europe/Minsk' => 'E. Europe Standard Time', 'Asia/Amman' => 'Jordan Standard Time', 'Asia/Beirut' => 'Middle East Standard Time',
            'Africa/Windhoek' => 'Namibia Standard Time', 'Africa/Nairobi' => 'E. Africa Standard Time', 'Asia/Baku' => 'Azerbaijan Standard Time',
            'Asia/Riyadh' => 'Arab Standard Time', 'Europe/Moscow' => 'Russian Standard Time', 'Asia/Baghdad' => 'Arabic Standard Time',
            'Asia/Tehran' => 'Iran Standard Time', 'Asia/Yerevan' => 'Armenian Standard Time', 'Asia/Kabul' => 'Afghanistan Standard Time',
            'Asia/Karachi' => 'Pakistan Standard Time', 'Asia/Yekaterinburg' => 'Ekaterinburg Standard Time', 'Asia/Calcutta' => 'India Standard Time',
            'Asia/Katmandu' => 'Nepal Standard Time', 'Asia/Colombo' => 'Sri Lanka Standard Time', 'Asia/Dhaka' => 'Central Asia Standard Time', 'Asia/Novosibirsk' => 'N. Central Asia Standard Time',
            'Asia/Rangoon' => 'Myanmar Standard Time', 'Asia/Bangkok' => 'SE Asia Standard Time', 'Asia/Krasnoyarsk' => 'North Asia Standard Time', 'Australia/Perth' => 'W. Australia Standard Time',
            'Asia/Taipei' => 'Taipei Standard Time', 'Asia/Singapore' => 'Singapore Standard Time', 'Asia/Shanghai' => 'China Standard Time',
            'Asia/Tokyo' => 'Tokyo Standard Time', 'Asia/Seoul' => 'Korea Standard Time', 'Asia/Yakutsk' => 'Yakutsk Standard Time', 'Australia/Darwin' => 'AUS Central Standard Time',
            'Australia/Adelaide' => 'Cen. Australia Standard Time', 'Australia/Brisbane' => 'E. Australia Standard Time',
            'Asia/Vladivostok' => 'Vladivostok Standard Time', 'Australia/Hobart' => 'Tasmania Standard Time', 'Australia/Sydney' => 'AUS Eastern Standard Time',
            'Pacific/Guadalcanal' => 'Central Pacific Standard Time', 'Pacific/Fiji' => 'Fiji Standard Time', 'Pacific/Auckland' => 'New Zealand Standard Time',
            'Pacific/Tongatapu' => 'Tonga Standard Time', 'Asia/Dubai' => 'Arabian Standard Time',
            'Europe/Budapest' => 'Central Europe Standard Time', 'Europe/Kiev' => 'FLE Standard Time',
            'Etc/GMT+12' => 'Dateline Standard Time', 'Asia/Tbilisi' => 'Georgian Standard Time',
            'Etc/GMT+5' => 'US Eastern Standard Time',
            'Pacific/Port_Moresby' => 'West Pacific Standard Time', 'America/La_Paz' => 'SA Western Standard Time',
            'Asia/Irkutsk' => 'North Asia East Standard Time', 'Etc/GMT+2' => 'Mid-Atlantic Standard Time',
            'Asia/Tashkent' => 'West Asia Standard Time', 'Indian/Mauritius' => 'Mauritius Standard Time',
            'Atlantic/Reykjavik' => 'Greenwich Standard Time', 'Etc/GMT' => 'UTC', 'Etc/GMT+11' => 'UTC-11',
            'America/Asuncion' => 'Paraguay Standard Time', 'America/Cayenne' => 'SA Eastern Standard Time',
            'Asia/Damascus' => 'Syria Standard Time', 'Asia/Dhaka' => 'Bangladesh Standard Time',
            'Asia/Almaty' => 'Central Asia Standard Time', 'Asia/Ulaanbaatar' => 'Ulaanbaatar Standard Time',
            'Asia/Kamchatka' => 'Kamchatka Standard Time', 'Etc/GMT-12' => 'UTC+12');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'windowstotimezone', 'Pacific/Fiji');
        $this->assertEquals("Fiji Standard Time", $value);
    }

    /**
     * test for reading territorytotimezone from locale
     * expected array
     */
    public function ztestTerritoryToTimezone()
    {
        $value = Cldr::getList('de_AT', 'territorytotimezone');
        $result = array('Africa/Abidjan' => 'CI', 'Africa/Accra' => 'GH', 'Africa/Addis_Ababa' => 'ET',
            'Africa/Algiers' => 'DZ', 'Africa/Asmera' => 'ER', 'Africa/Bamako' => 'ML', 'Africa/Bangui' => 'CF',
            'Africa/Banjul' => 'GM', 'Africa/Bissau' => 'GW', 'Africa/Blantyre' => 'MW', 'Africa/Brazzaville' => 'CG',
            'Africa/Bujumbura' => 'BI', 'Africa/Cairo' => 'EG', 'Africa/Casablanca' => 'MA', 'Africa/Ceuta' => 'ES',
            'Africa/Conakry' => 'GN', 'Africa/Dakar' => 'SN', 'Africa/Dar_es_Salaam' => 'TZ', 'Africa/Djibouti' => 'DJ',
            'Africa/Douala' => 'CM', 'Africa/El_Aaiun' => 'EH', 'Africa/Freetown' => 'SL', 'Africa/Gaborone' => 'BW',
            'Africa/Harare' => 'ZW', 'Africa/Johannesburg' => 'ZA', 'Africa/Kampala' => 'UG', 'Africa/Khartoum' => 'SD',
            'Africa/Kigali' => 'RW', 'Africa/Kinshasa' => 'CD', 'Africa/Lagos' => 'NG', 'Africa/Libreville' => 'GA',
            'Africa/Lome' => 'TG', 'Africa/Luanda' => 'AO', 'Africa/Lubumbashi' => 'CD', 'Africa/Lusaka' => 'ZM',
            'Africa/Malabo' => 'GQ', 'Africa/Maputo' => 'MZ', 'Africa/Maseru' => 'LS', 'Africa/Mbabane' => 'SZ',
            'Africa/Mogadishu' => 'SO', 'Africa/Monrovia' => 'LR', 'Africa/Nairobi' => 'KE', 'Africa/Ndjamena' => 'TD',
            'Africa/Niamey' => 'NE', 'Africa/Nouakchott' => 'MR', 'Africa/Ouagadougou' => 'BF', 'Africa/Porto-Novo' => 'BJ',
            'Africa/Sao_Tome' => 'ST', 'Africa/Tripoli' => 'LY', 'Africa/Tunis' => 'TN', 'Africa/Windhoek' => 'NA',
            'America/Adak' => 'US', 'America/Anchorage' => 'US', 'America/Anguilla' => 'AI', 'America/Antigua' => 'AG',
            'America/Araguaina' => 'BR', 'America/Argentina/La_Rioja' => 'AR', 'America/Argentina/Rio_Gallegos' => 'AR',
            'America/Argentina/San_Juan' => 'AR', 'America/Argentina/Tucuman' => 'AR', 'America/Argentina/Ushuaia' => 'AR',
            'America/Aruba' => 'AW', 'America/Asuncion' => 'PY', 'America/Bahia' => 'BR', 'America/Barbados' => 'BB',
            'America/Belem' => 'BR', 'America/Belize' => 'BZ', 'America/Blanc-Sablon' => 'CA', 'America/Boa_Vista' => 'BR',
            'America/Bogota' => 'CO', 'America/Boise' => 'US', 'America/Buenos_Aires' => 'AR', 'America/Cambridge_Bay' => 'CA',
            'America/Campo_Grande' => 'BR', 'America/Cancun' => 'MX', 'America/Caracas' => 'VE', 'America/Catamarca' => 'AR',
            'America/Cayenne' => 'GF', 'America/Cayman' => 'KY', 'America/Chicago' => 'US', 'America/Chihuahua' => 'MX',
            'America/Coral_Harbour' => 'CA', 'America/Cordoba' => 'AR', 'America/Costa_Rica' => 'CR', 'America/Cuiaba' => 'BR',
            'America/Curacao' => 'AN', 'America/Danmarkshavn' => 'GL', 'America/Dawson' => 'CA', 'America/Dawson_Creek' => 'CA',
            'America/Denver' => 'US', 'America/Detroit' => 'US', 'America/Dominica' => 'DM', 'America/Edmonton' => 'CA',
            'America/Eirunepe' => 'BR', 'America/El_Salvador' => 'SV', 'America/Fortaleza' => 'BR', 'America/Glace_Bay' => 'CA',
            'America/Godthab' => 'GL', 'America/Goose_Bay' => 'CA', 'America/Grand_Turk' => 'TC', 'America/Grenada' => 'GD',
            'America/Guadeloupe' => 'GP', 'America/Guatemala' => 'GT', 'America/Guayaquil' => 'EC', 'America/Guyana' => 'GY',
            'America/Halifax' => 'CA', 'America/Havana' => 'CU', 'America/Hermosillo' => 'MX', 'America/Indiana/Knox' => 'US',
            'America/Indiana/Marengo' => 'US', 'America/Indiana/Petersburg' => 'US', 'America/Indiana/Vevay' => 'US',
            'America/Indiana/Vincennes' => 'US', 'America/Indiana/Winamac' => 'US', 'America/Indianapolis' => 'US',
            'America/Inuvik' => 'CA', 'America/Iqaluit' => 'CA', 'America/Jamaica' => 'JM', 'America/Jujuy' => 'AR',
            'America/Juneau' => 'US', 'America/Kentucky/Monticello' => 'US', 'America/La_Paz' => 'BO', 'America/Lima' => 'PE',
            'America/Los_Angeles' => 'US', 'America/Louisville' => 'US', 'America/Maceio' => 'BR', 'America/Managua' => 'NI',
            'America/Manaus' => 'BR', 'America/Martinique' => 'MQ', 'America/Mazatlan' => 'MX', 'America/Mendoza' => 'AR',
            'America/Menominee' => 'US', 'America/Merida' => 'MX', 'America/Mexico_City' => 'MX', 'America/Miquelon' => 'PM',
            'America/Moncton' => 'CA', 'America/Monterrey' => 'MX', 'America/Montevideo' => 'UY', 'America/Montreal' => 'CA',
            'America/Montserrat' => 'MS', 'America/Nassau' => 'BS', 'America/New_York' => 'US', 'America/Nipigon' => 'CA',
            'America/Nome' => 'US', 'America/Noronha' => 'BR', 'America/North_Dakota/Center' => 'US', 'America/North_Dakota/New_Salem' => 'US',
            'America/Panama' => 'PA', 'America/Pangnirtung' => 'CA', 'America/Paramaribo' => 'SR', 'America/Phoenix' => 'US',
            'America/Port_of_Spain' => 'TT', 'America/Port-au-Prince' => 'HT', 'America/Porto_Velho' => 'BR',
            'America/Puerto_Rico' => 'PR', 'America/Rainy_River' => 'CA', 'America/Rankin_Inlet' => 'CA',
            'America/Recife' => 'BR', 'America/Regina' => 'CA', 'America/Rio_Branco' => 'BR', 'America/Santiago' => 'CL',
            'America/Santo_Domingo' => 'DO', 'America/Sao_Paulo' => 'BR', 'America/Scoresbysund' => 'GL',
            'America/Shiprock' => 'US', 'America/St_Johns' => 'CA', 'America/St_Kitts' => 'KN', 'America/St_Lucia' => 'LC',
            'America/St_Thomas' => 'VI', 'America/St_Vincent' => 'VC', 'America/Swift_Current' => 'CA',
            'America/Tegucigalpa' => 'HN', 'America/Thule' => 'GL', 'America/Thunder_Bay' => 'CA', 'America/Tijuana' => 'MX',
            'America/Toronto' => 'CA', 'America/Tortola' => 'VG', 'America/Vancouver' => 'CA', 'America/Whitehorse' => 'CA',
            'America/Winnipeg' => 'CA', 'America/Yakutat' => 'US', 'America/Yellowknife' => 'CA', 'Antarctica/Casey' => 'AQ',
            'Antarctica/Davis' => 'AQ', 'Antarctica/DumontDUrville' => 'AQ', 'Antarctica/Mawson' => 'AQ',
            'Antarctica/McMurdo' => 'AQ', 'Antarctica/Palmer' => 'AQ', 'Antarctica/Rothera' => 'AQ', 'Antarctica/South_Pole' => 'AQ',
            'Antarctica/Syowa' => 'AQ', 'Antarctica/Vostok' => 'AQ', 'Arctic/Longyearbyen' => 'SJ', 'Asia/Aden' => 'YE',
            'Asia/Almaty' => 'KZ', 'Asia/Amman' => 'JO', 'Asia/Anadyr' => 'RU', 'Asia/Aqtau' => 'KZ', 'Asia/Aqtobe' => 'KZ',
            'Asia/Ashgabat' => 'TM', 'Asia/Baghdad' => 'IQ', 'Asia/Bahrain' => 'BH', 'Asia/Baku' => 'AZ',
            'Asia/Bangkok' => 'TH', 'Asia/Beirut' => 'LB', 'Asia/Bishkek' => 'KG', 'Asia/Brunei' => 'BN', 'Asia/Calcutta' => 'IN',
            'Asia/Choibalsan' => 'MN', 'Asia/Chongqing' => 'CN', 'Asia/Colombo' => 'LK', 'Asia/Damascus' => 'SY',
            'Asia/Dhaka' => 'BD', 'Asia/Dili' => 'TL', 'Asia/Dubai' => 'AE', 'Asia/Dushanbe' => 'TJ', 'Asia/Gaza' => 'PS',
            'Asia/Harbin' => 'CN', 'Asia/Hong_Kong' => 'HK', 'Asia/Hovd' => 'MN', 'Asia/Irkutsk' => 'RU', 'Asia/Jakarta' => 'ID',
            'Asia/Jayapura' => 'ID', 'Asia/Jerusalem' => 'IL', 'Asia/Kabul' => 'AF', 'Asia/Kamchatka' => 'RU',
            'Asia/Karachi' => 'PK', 'Asia/Kashgar' => 'CN', 'Asia/Katmandu' => 'NP', 'Asia/Krasnoyarsk' => 'RU',
            'Asia/Kuala_Lumpur' => 'MY', 'Asia/Kuching' => 'MY', 'Asia/Kuwait' => 'KW', 'Asia/Macau' => 'MO',
            'Asia/Magadan' => 'RU', 'Asia/Makassar' => 'ID', 'Asia/Manila' => 'PH', 'Asia/Muscat' => 'OM', 'Asia/Nicosia' => 'CY',
            'Asia/Novosibirsk' => 'RU', 'Asia/Omsk' => 'RU', 'Asia/Oral' => 'KZ', 'Asia/Phnom_Penh' => 'KH',
            'Asia/Pontianak' => 'ID', 'Asia/Pyongyang' => 'KP', 'Asia/Qatar' => 'QA', 'Asia/Qyzylorda' => 'KZ',
            'Asia/Rangoon' => 'MM', 'Asia/Riyadh' => 'SA', 'Asia/Saigon' => 'VN', 'Asia/Sakhalin' => 'RU', 'Asia/Samarkand' => 'UZ',
            'Asia/Seoul' => 'KR', 'Asia/Shanghai' => 'CN', 'Asia/Singapore' => 'SG', 'Asia/Taipei' => 'TW',
            'Asia/Tashkent' => 'UZ', 'Asia/Tbilisi' => 'GE', 'Asia/Tehran' => 'IR', 'Asia/Thimphu' => 'BT',
            'Asia/Tokyo' => 'JP', 'Asia/Ulaanbaatar' => 'MN', 'Asia/Urumqi' => 'CN', 'Asia/Vientiane' => 'LA',
            'Asia/Vladivostok' => 'RU', 'Asia/Yakutsk' => 'RU', 'Asia/Yekaterinburg' => 'RU', 'Asia/Yerevan' => 'AM',
            'Atlantic/Azores' => 'PT', 'Atlantic/Bermuda' => 'BM', 'Atlantic/Canary' => 'ES', 'Atlantic/Cape_Verde' => 'CV',
            'Atlantic/Faeroe' => 'FO', 'Atlantic/Madeira' => 'PT', 'Atlantic/Reykjavik' => 'IS',
            'Atlantic/South_Georgia' => 'GS', 'Atlantic/St_Helena' => 'SH', 'Atlantic/Stanley' => 'FK', 'Australia/Adelaide' => 'AU',
            'Australia/Brisbane' => 'AU', 'Australia/Broken_Hill' => 'AU', 'Australia/Currie' => 'AU', 'Australia/Darwin' => 'AU',
            'Australia/Eucla' => 'AU', 'Australia/Hobart' => 'AU', 'Australia/Lindeman' => 'AU', 'Australia/Lord_Howe' => 'AU',
            'Australia/Melbourne' => 'AU', 'Australia/Perth' => 'AU', 'Australia/Sydney' => 'AU', 'Etc/GMT' => '001',
            'Etc/GMT-1' => '001', 'Etc/GMT-2' => '001', 'Etc/GMT-3' => '001', 'Etc/GMT-4' => '001', 'Etc/GMT-5' => '001',
            'Etc/GMT-6' => '001', 'Etc/GMT-7' => '001', 'Etc/GMT-8' => '001', 'Etc/GMT-9' => '001', 'Etc/GMT-10' => '001',
            'Etc/GMT-11' => '001', 'Etc/GMT-12' => '001', 'Etc/GMT-13' => '001', 'Etc/GMT-14' => '001', 'Etc/GMT+1' => '001',
            'Etc/GMT+2' => '001', 'Etc/GMT+3' => '001', 'Etc/GMT+4' => '001', 'Etc/GMT+5' => '001', 'Etc/GMT+6' => '001',
            'Etc/GMT+7' => '001', 'Etc/GMT+8' => '001', 'Etc/GMT+9' => '001', 'Etc/GMT+10' => '001', 'Etc/GMT+11' => '001',
            'Etc/GMT+12' => '001', 'Etc/Unknown' => '001', 'Europe/Amsterdam' => 'NL', 'Europe/Andorra' => 'AD',
            'Europe/Athens' => 'GR', 'Europe/Belgrade' => 'RS', 'Europe/Berlin' => 'DE', 'Europe/Bratislava' => 'SK',
            'Europe/Brussels' => 'BE', 'Europe/Bucharest' => 'RO', 'Europe/Budapest' => 'HU', 'Europe/Chisinau' => 'MD',
            'Europe/Copenhagen' => 'DK', 'Europe/Dublin' => 'IE', 'Europe/Gibraltar' => 'GI', 'Europe/Guernsey' => 'GG',
            'Europe/Helsinki' => 'FI', 'Europe/Isle_of_Man' => 'IM', 'Europe/Istanbul' => 'TR', 'Europe/Jersey' => 'JE',
            'Europe/Kaliningrad' => 'RU', 'Europe/Kiev' => 'UA', 'Europe/Lisbon' => 'PT', 'Europe/Ljubljana' => 'SI',
            'Europe/London' => 'GB', 'Europe/Luxembourg' => 'LU', 'Europe/Madrid' => 'ES', 'Europe/Malta' => 'MT',
            'Europe/Mariehamn' => 'AX', 'Europe/Minsk' => 'BY', 'Europe/Monaco' => 'MC', 'Europe/Moscow' => 'RU',
            'Europe/Oslo' => 'NO', 'Europe/Paris' => 'FR', 'Europe/Podgorica' => 'ME', 'Europe/Prague' => 'CZ',
            'Europe/Riga' => 'LV', 'Europe/Rome' => 'IT', 'Europe/Samara' => 'RU', 'Europe/San_Marino' => 'SM',
            'Europe/Sarajevo' => 'BA', 'Europe/Simferopol' => 'UA', 'Europe/Skopje' => 'MK', 'Europe/Sofia' => 'BG',
            'Europe/Stockholm' => 'SE', 'Europe/Tallinn' => 'EE', 'Europe/Tirane' => 'AL', 'Europe/Uzhgorod' => 'UA',
            'Europe/Vaduz' => 'LI', 'Europe/Vatican' => 'VA', 'Europe/Vienna' => 'AT', 'Europe/Vilnius' => 'LT',
            'Europe/Volgograd' => 'RU', 'Europe/Warsaw' => 'PL', 'Europe/Zagreb' => 'HR', 'Europe/Zaporozhye' => 'UA',
            'Europe/Zurich' => 'CH', 'Indian/Antananarivo' => 'MG', 'Indian/Chagos' => 'IO', 'Indian/Christmas' => 'CX',
            'Indian/Cocos' => 'CC', 'Indian/Comoro' => 'KM', 'Indian/Kerguelen' => 'TF', 'Indian/Mahe' => 'SC',
            'Indian/Maldives' => 'MV', 'Indian/Mauritius' => 'MU', 'Indian/Mayotte' => 'YT', 'Indian/Reunion' => 'RE',
            'Pacific/Apia' => 'WS', 'Pacific/Auckland' => 'NZ', 'Pacific/Chatham' => 'NZ', 'Pacific/Easter' => 'CL',
            'Pacific/Efate' => 'VU', 'Pacific/Enderbury' => 'KI', 'Pacific/Fakaofo' => 'TK', 'Pacific/Fiji' => 'FJ',
            'Pacific/Funafuti' => 'TV', 'Pacific/Galapagos' => 'EC', 'Pacific/Gambier' => 'PF', 'Pacific/Guadalcanal' => 'SB',
            'Pacific/Guam' => 'GU', 'Pacific/Honolulu' => 'US', 'Pacific/Johnston' => 'UM', 'Pacific/Kiritimati' => 'KI',
            'Pacific/Kosrae' => 'FM', 'Pacific/Kwajalein' => 'MH', 'Pacific/Majuro' => 'MH', 'Pacific/Marquesas' => 'PF',
            'Pacific/Midway' => 'UM', 'Pacific/Nauru' => 'NR', 'Pacific/Niue' => 'NU', 'Pacific/Norfolk' => 'NF',
            'Pacific/Noumea' => 'NC', 'Pacific/Pago_Pago' => 'AS', 'Pacific/Palau' => 'PW', 'Pacific/Pitcairn' => 'PN',
            'Pacific/Ponape' => 'FM', 'Pacific/Port_Moresby' => 'PG', 'Pacific/Rarotonga' => 'CK', 'Pacific/Saipan' => 'MP',
            'Pacific/Tahiti' => 'PF', 'Pacific/Tarawa' => 'KI', 'Pacific/Tongatapu' => 'TO', 'Pacific/Truk' => 'FM',
            'Pacific/Wake' => 'UM', 'Pacific/Wallis' => 'WF', 'America/Indiana/Tell_City' => 'US', 'America/Resolute' => 'CA',
            'America/St_Barthelemy' => 'BL', 'America/Santarem' => 'BR', 'America/Marigot' => 'MF',
            'America/Argentina/San_Luis' => 'AR', 'America/Argentina/Salta' => 'AR');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'territorytotimezone', 'Pacific/Fiji');
        $this->assertEquals("FJ", $value);
    }

    /**
     * test for reading timezonetoterritory from locale
     * expected array
     */
    public function ztestTimezoneToTerritory()
    {
        $value = Cldr::getList('de_AT', 'timezonetoterritory');
        $result = array('CI' => 'Africa/Abidjan', 'GH' => 'Africa/Accra', 'ET' => 'Africa/Addis_Ababa',
            'DZ' => 'Africa/Algiers', 'ER' => 'Africa/Asmera', 'ML' => 'Africa/Bamako', 'CF' => 'Africa/Bangui',
            'GM' => 'Africa/Banjul', 'GW' => 'Africa/Bissau', 'MW' => 'Africa/Blantyre', 'CG' => 'Africa/Brazzaville',
            'BI' => 'Africa/Bujumbura', 'EG' => 'Africa/Cairo', 'MA' => 'Africa/Casablanca', 'ES' => 'Africa/Ceuta',
            'GN' => 'Africa/Conakry', 'SN' => 'Africa/Dakar', 'TZ' => 'Africa/Dar_es_Salaam', 'DJ' => 'Africa/Djibouti',
            'CM' => 'Africa/Douala', 'EH' => 'Africa/El_Aaiun', 'SL' => 'Africa/Freetown', 'BW' => 'Africa/Gaborone',
            'ZW' => 'Africa/Harare', 'ZA' => 'Africa/Johannesburg', 'UG' => 'Africa/Kampala', 'SD' => 'Africa/Khartoum',
            'RW' => 'Africa/Kigali', 'CD' => 'Africa/Kinshasa', 'NG' => 'Africa/Lagos', 'GA' => 'Africa/Libreville',
            'TG' => 'Africa/Lome', 'AO' => 'Africa/Luanda', 'ZM' => 'Africa/Lusaka', 'GQ' => 'Africa/Malabo',
            'MZ' => 'Africa/Maputo', 'LS' => 'Africa/Maseru', 'SZ' => 'Africa/Mbabane', 'SO' => 'Africa/Mogadishu',
            'LR' => 'Africa/Monrovia', 'KE' => 'Africa/Nairobi', 'TD' => 'Africa/Ndjamena', 'NE' => 'Africa/Niamey',
            'MR' => 'Africa/Nouakchott', 'BF' => 'Africa/Ouagadougou', 'BJ' => 'Africa/Porto-Novo', 'ST' => 'Africa/Sao_Tome',
            'LY' => 'Africa/Tripoli', 'TN' => 'Africa/Tunis', 'NA' => 'Africa/Windhoek', 'US' => 'America/Adak',
            'AI' => 'America/Anguilla', 'AG' => 'America/Antigua', 'BR' => 'America/Araguaina', 'AR' => 'America/Argentina/La_Rioja',
            'AW' => 'America/Aruba', 'PY' => 'America/Asuncion', 'BB' => 'America/Barbados', 'BZ' => 'America/Belize',
            'CA' => 'America/Blanc-Sablon', 'CO' => 'America/Bogota', 'MX' => 'America/Cancun', 'VE' => 'America/Caracas',
            'GF' => 'America/Cayenne', 'KY' => 'America/Cayman', 'CR' => 'America/Costa_Rica', 'AN' => 'America/Curacao',
            'GL' => 'America/Danmarkshavn', 'DM' => 'America/Dominica', 'SV' => 'America/El_Salvador', 'TC' => 'America/Grand_Turk',
            'GD' => 'America/Grenada', 'GP' => 'America/Guadeloupe', 'GT' => 'America/Guatemala', 'EC' => 'America/Guayaquil',
            'GY' => 'America/Guyana', 'CU' => 'America/Havana', 'JM' => 'America/Jamaica', 'BO' => 'America/La_Paz',
            'PE' => 'America/Lima', 'NI' => 'America/Managua', 'MQ' => 'America/Martinique', 'PM' => 'America/Miquelon',
            'UY' => 'America/Montevideo', 'MS' => 'America/Montserrat', 'BS' => 'America/Nassau', 'PA' => 'America/Panama',
            'SR' => 'America/Paramaribo', 'TT' => 'America/Port_of_Spain', 'HT' => 'America/Port-au-Prince',
            'PR' => 'America/Puerto_Rico', 'CL' => 'America/Santiago', 'DO' => 'America/Santo_Domingo', 'KN' => 'America/St_Kitts',
            'LC' => 'America/St_Lucia', 'VI' => 'America/St_Thomas', 'VC' => 'America/St_Vincent', 'HN' => 'America/Tegucigalpa',
            'VG' => 'America/Tortola', 'AQ' => 'Antarctica/Casey', 'SJ' => 'Arctic/Longyearbyen', 'YE' => 'Asia/Aden',
            'KZ' => 'Asia/Almaty', 'JO' => 'Asia/Amman', 'RU' => 'Asia/Anadyr', 'TM' => 'Asia/Ashgabat', 'IQ' => 'Asia/Baghdad',
            'BH' => 'Asia/Bahrain', 'AZ' => 'Asia/Baku', 'TH' => 'Asia/Bangkok', 'LB' => 'Asia/Beirut', 'KG' => 'Asia/Bishkek',
            'BN' => 'Asia/Brunei', 'IN' => 'Asia/Calcutta', 'MN' => 'Asia/Choibalsan', 'CN' => 'Asia/Chongqing',
            'LK' => 'Asia/Colombo', 'SY' => 'Asia/Damascus', 'BD' => 'Asia/Dhaka', 'TL' => 'Asia/Dili', 'AE' => 'Asia/Dubai',
            'TJ' => 'Asia/Dushanbe', 'PS' => 'Asia/Gaza', 'HK' => 'Asia/Hong_Kong', 'ID' => 'Asia/Jakarta', 'IL' => 'Asia/Jerusalem',
            'AF' => 'Asia/Kabul', 'PK' => 'Asia/Karachi', 'NP' => 'Asia/Katmandu', 'MY' => 'Asia/Kuala_Lumpur',
            'KW' => 'Asia/Kuwait', 'MO' => 'Asia/Macau', 'PH' => 'Asia/Manila', 'OM' => 'Asia/Muscat', 'CY' => 'Asia/Nicosia',
            'KH' => 'Asia/Phnom_Penh', 'KP' => 'Asia/Pyongyang', 'QA' => 'Asia/Qatar', 'MM' => 'Asia/Rangoon',
            'SA' => 'Asia/Riyadh', 'VN' => 'Asia/Saigon', 'UZ' => 'Asia/Samarkand', 'KR' => 'Asia/Seoul', 'SG' => 'Asia/Singapore',
            'TW' => 'Asia/Taipei', 'GE' => 'Asia/Tbilisi', 'IR' => 'Asia/Tehran', 'BT' => 'Asia/Thimphu', 'JP' => 'Asia/Tokyo',
            'LA' => 'Asia/Vientiane', 'AM' => 'Asia/Yerevan', 'PT' => 'Atlantic/Azores', 'BM' => 'Atlantic/Bermuda',
            'CV' => 'Atlantic/Cape_Verde', 'FO' => 'Atlantic/Faeroe', 'IS' => 'Atlantic/Reykjavik', 'GS' => 'Atlantic/South_Georgia',
            'SH' => 'Atlantic/St_Helena', 'FK' => 'Atlantic/Stanley', 'AU' => 'Australia/Adelaide', '001' => 'Etc/GMT',
            'NL' => 'Europe/Amsterdam', 'AD' => 'Europe/Andorra', 'GR' => 'Europe/Athens', 'RS' => 'Europe/Belgrade',
            'DE' => 'Europe/Berlin', 'SK' => 'Europe/Bratislava', 'BE' => 'Europe/Brussels', 'RO' => 'Europe/Bucharest',
            'HU' => 'Europe/Budapest', 'MD' => 'Europe/Chisinau', 'DK' => 'Europe/Copenhagen', 'IE' => 'Europe/Dublin',
            'GI' => 'Europe/Gibraltar', 'GG' => 'Europe/Guernsey', 'FI' => 'Europe/Helsinki', 'IM' => 'Europe/Isle_of_Man',
            'TR' => 'Europe/Istanbul', 'JE' => 'Europe/Jersey', 'UA' => 'Europe/Kiev', 'SI' => 'Europe/Ljubljana',
            'GB' => 'Europe/London', 'LU' => 'Europe/Luxembourg', 'MT' => 'Europe/Malta', 'AX' => 'Europe/Mariehamn',
            'BY' => 'Europe/Minsk', 'MC' => 'Europe/Monaco', 'NO' => 'Europe/Oslo', 'FR' => 'Europe/Paris', 'ME' => 'Europe/Podgorica',
            'CZ' => 'Europe/Prague', 'LV' => 'Europe/Riga', 'IT' => 'Europe/Rome', 'SM' => 'Europe/San_Marino',
            'BA' => 'Europe/Sarajevo', 'MK' => 'Europe/Skopje', 'BG' => 'Europe/Sofia', 'SE' => 'Europe/Stockholm',
            'EE' => 'Europe/Tallinn', 'AL' => 'Europe/Tirane', 'LI' => 'Europe/Vaduz', 'VA' => 'Europe/Vatican',
            'AT' => 'Europe/Vienna', 'LT' => 'Europe/Vilnius', 'PL' => 'Europe/Warsaw', 'HR' => 'Europe/Zagreb',
            'CH' => 'Europe/Zurich', 'MG' => 'Indian/Antananarivo', 'IO' => 'Indian/Chagos', 'CX' => 'Indian/Christmas',
            'CC' => 'Indian/Cocos', 'KM' => 'Indian/Comoro', 'TF' => 'Indian/Kerguelen', 'SC' => 'Indian/Mahe',
            'MV' => 'Indian/Maldives', 'MU' => 'Indian/Mauritius', 'YT' => 'Indian/Mayotte', 'RE' => 'Indian/Reunion',
            'WS' => 'Pacific/Apia', 'NZ' => 'Pacific/Auckland', 'VU' => 'Pacific/Efate', 'KI' => 'Pacific/Enderbury',
            'TK' => 'Pacific/Fakaofo', 'FJ' => 'Pacific/Fiji', 'TV' => 'Pacific/Funafuti', 'PF' => 'Pacific/Gambier',
            'SB' => 'Pacific/Guadalcanal', 'GU' => 'Pacific/Guam', 'UM' => 'Pacific/Johnston', 'FM' => 'Pacific/Kosrae',
            'MH' => 'Pacific/Kwajalein', 'NR' => 'Pacific/Nauru', 'NU' => 'Pacific/Niue', 'NF' => 'Pacific/Norfolk',
            'NC' => 'Pacific/Noumea', 'AS' => 'Pacific/Pago_Pago', 'PW' => 'Pacific/Palau', 'PN' => 'Pacific/Pitcairn',
            'PG' => 'Pacific/Port_Moresby', 'CK' => 'Pacific/Rarotonga', 'MP' => 'Pacific/Saipan', 'TO' => 'Pacific/Tongatapu',
            'WF' => 'Pacific/Wallis', 'MF' => 'America/Marigot', 'BL' => 'America/St_Barthelemy');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'timezonetoterritory', 'FJ');
        $this->assertEquals("Pacific/Fiji", $value);
    }

    /**
     * test for reading citytotimezone from locale
     * expected array
     */
    public function testCityToTimezone()
    {
        $value = Cldr::getList('de_AT', 'citytotimezone');
        $result = array('Etc/Unknown' => 'Unbekannt', 'Europe/Tirane' => 'Tirana', 'Asia/Yerevan' => 'Erivan',
            'America/Curacao' => 'Curaçao', 'Antarctica/South_Pole' => 'Südpol', 'Antarctica/Vostok' => 'Wostok',
            'Antarctica/DumontDUrville' => "Dumont D'Urville", 'Europe/Vienna' => 'Wien', 'Europe/Brussels' => 'Brüssel',
            'Africa/Ouagadougou' => 'Wagadugu', 'Atlantic/Bermuda' => 'Bermudas', 'America/St_Johns' => "St. John's",
            'Europe/Zurich' => 'Zürich', 'Pacific/Easter' => 'Osterinsel', 'America/Havana' => 'Havanna',
            'Atlantic/Cape_Verde' => 'Kap Verde', 'Indian/Christmas' => 'Weihnachts-Inseln', 'Asia/Nicosia' => 'Nikosia',
            'Africa/Djibouti' => 'Dschibuti', 'Europe/Copenhagen' => 'Kopenhagen', 'Africa/Algiers' => 'Algier',
            'Africa/Cairo' => 'Kairo', 'Africa/El_Aaiun' => 'El Aaiún', 'Atlantic/Canary' => 'Kanaren',
            'Africa/Addis_Ababa' => 'Addis Abeba', 'Pacific/Fiji' => 'Fidschi', 'Atlantic/Faeroe' => 'Färöer',
            'Asia/Tbilisi' => 'Tiflis', 'Africa/Accra' => 'Akkra',
            'Europe/Athens' => 'Athen', 'Atlantic/South_Georgia' => 'Süd-Georgien', 'Asia/Hong_Kong' => 'Hongkong',
            'Asia/Baghdad' => 'Bagdad', 'Asia/Tehran' => 'Teheran', 'Europe/Rome' => 'Rom', 'America/Jamaica' => 'Jamaika',
            'Asia/Tokyo' => 'Tokio', 'Asia/Bishkek' => 'Bischkek', 'Indian/Comoro' => 'Komoren', 'America/St_Kitts' => 'St. Kitts',
            'Asia/Pyongyang' => 'Pjöngjang', 'America/Cayman' => 'Kaimaninseln', 'Asia/Aqtobe' => 'Aktobe',
            'America/St_Lucia' => 'St. Lucia', 'Europe/Vilnius' => 'Wilna', 'Europe/Luxembourg' => 'Luxemburg',
            'Africa/Tripoli' => 'Tripolis', 'Europe/Chisinau' => 'Kischinau',
            'Asia/Macau' => 'Macao', 'Indian/Maldives' => 'Malediven', 'America/Mexico_City' => 'Mexiko-Stadt',
            'Africa/Niamey' => 'Niger', 'Asia/Muscat' => 'Muskat', 'Europe/Warsaw' => 'Warschau',
            'Atlantic/Azores' => 'Azoren', 'Europe/Lisbon' => 'Lissabon', 'America/Asuncion' => 'Asunción',
            'Asia/Qatar' => 'Katar', 'Indian/Reunion' => 'Réunion', 'Europe/Bucharest' => 'Bukarest',
            'Europe/Moscow' => 'Moskau', 'Asia/Yekaterinburg' => 'Jekaterinburg', 'Asia/Novosibirsk' => 'Nowosibirsk',
            'Asia/Krasnoyarsk' => 'Krasnojarsk', 'Asia/Yakutsk' => 'Jakutsk', 'Asia/Vladivostok' => 'Wladiwostok',
            'Asia/Sakhalin' => 'Sachalin', 'Asia/Kamchatka' => 'Kamtschatka', 'Asia/Riyadh' => 'Riad',
            'Africa/Khartoum' => 'Khartum', 'Asia/Singapore' => 'Singapur', 'Atlantic/St_Helena' => 'St. Helena',
            'Africa/Mogadishu' => 'Mogadischu', 'Africa/Sao_Tome' => 'São Tomé', 'America/El_Salvador' => 'Salvador',
            'Asia/Damascus' => 'Damaskus', 'Asia/Dushanbe' => 'Duschanbe', 'America/Port_of_Spain' => 'Port-of-Spain',
            'Asia/Taipei' => 'Taipeh', 'Africa/Dar_es_Salaam' => 'Daressalam', 'Europe/Uzhgorod' => 'Uschgorod',
            'Europe/Kiev' => 'Kiew', 'Europe/Zaporozhye' => 'Saporischja', 'Europe/Volgograd' => 'Wolgograd',
            'Asia/Tashkent' => 'Taschkent', 'America/St_Vincent' => 'St. Vincent', 'America/St_Thomas' => 'St. Thomas',
            'America/Kentucky/Monticello' => 'Monticello, Kentucky', 'America/Indiana/Vevay' => 'Vevay, Indiana',
            'America/Indiana/Marengo' => 'Marengo, Indiana', 'America/Indiana/Winamac' => 'Winamac, Indiana',
            'America/Indiana/Tell_City' => 'Tell City, Indiana', 'America/Indiana/Petersburg' => 'Petersburg, Indiana',
            'America/Indiana/Vincennes' => 'Vincennes, Indiana', 'America/North_Dakota/Center' => 'Center, North Dakota',
            'America/North_Dakota/New_Salem' => 'New Salem, North Dakota', 'America/Indiana/Knox' => 'Knox', 'Asia/Urumqi' => 'Ürümqi');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'citytotimezone', 'Pacific/Fiji');
        $this->assertEquals("Fidschi", $value);
    }

    /**
     * test for reading timezonetocity from locale
     * expected array
     */
    public function testTimezoneToCity()
    {
        $value = Cldr::getList('de_AT', 'timezonetocity');
        $result = array('Unbekannt' => 'Etc/Unknown', 'Tirana' => 'Europe/Tirane', 'Erivan' => 'Asia/Yerevan',
            'Curaçao' => 'America/Curacao', 'Südpol' => 'Antarctica/South_Pole', 'Wostok' => 'Antarctica/Vostok',
            "Dumont D'Urville" => 'Antarctica/DumontDUrville', 'Wien' => 'Europe/Vienna', 'Brüssel' => 'Europe/Brussels',
            'Wagadugu' => 'Africa/Ouagadougou', 'Bermudas' => 'Atlantic/Bermuda', "St. John's" => 'America/St_Johns',
            'Zürich' => 'Europe/Zurich', 'Osterinsel' => 'Pacific/Easter', 'Havanna' => 'America/Havana',
            'Kap Verde' => 'Atlantic/Cape_Verde', 'Weihnachts-Inseln' => 'Indian/Christmas', 'Nikosia' => 'Asia/Nicosia',
            'Dschibuti' => 'Africa/Djibouti', 'Kopenhagen' => 'Europe/Copenhagen', 'Algier' => 'Africa/Algiers',
            'Kairo' => 'Africa/Cairo', 'El Aaiún' => 'Africa/El_Aaiun', 'Kanaren' => 'Atlantic/Canary',
            'Addis Abeba' => 'Africa/Addis_Ababa', 'Fidschi' => 'Pacific/Fiji', 'Färöer' => 'Atlantic/Faeroe',
            'Tiflis' => 'Asia/Tbilisi', 'Akkra' => 'Africa/Accra',
            'Athen' => 'Europe/Athens', 'Süd-Georgien' => 'Atlantic/South_Georgia', 'Hongkong' => 'Asia/Hong_Kong',
            'Bagdad' => 'Asia/Baghdad', 'Teheran' => 'Asia/Tehran', 'Rom' => 'Europe/Rome', 'Jamaika' => 'America/Jamaica',
            'Tokio' => 'Asia/Tokyo', 'Bischkek' => 'Asia/Bishkek', 'Komoren' => 'Indian/Comoro', 'St. Kitts' => 'America/St_Kitts',
            'Pjöngjang' => 'Asia/Pyongyang', 'Kaimaninseln' => 'America/Cayman', 'Aktobe' => 'Asia/Aqtobe',
            'St. Lucia' => 'America/St_Lucia', 'Wilna' => 'Europe/Vilnius', 'Luxemburg' => 'Europe/Luxembourg',
            'Tripolis' => 'Africa/Tripoli', 'Kischinau' => 'Europe/Chisinau',
            'Macao' => 'Asia/Macau', 'Malediven' => 'Indian/Maldives', 'Mexiko-Stadt' => 'America/Mexico_City',
            'Niger' => 'Africa/Niamey', 'Muskat' => 'Asia/Muscat', 'Warschau' => 'Europe/Warsaw', 'Azoren' => 'Atlantic/Azores',
            'Lissabon' => 'Europe/Lisbon', 'Asunción' => 'America/Asuncion', 'Katar' => 'Asia/Qatar',
            'Réunion' => 'Indian/Reunion', 'Bukarest' => 'Europe/Bucharest', 'Moskau' => 'Europe/Moscow',
            'Jekaterinburg' => 'Asia/Yekaterinburg', 'Nowosibirsk' => 'Asia/Novosibirsk', 'Krasnojarsk' => 'Asia/Krasnoyarsk',
            'Jakutsk' => 'Asia/Yakutsk', 'Wladiwostok' => 'Asia/Vladivostok', 'Sachalin' => 'Asia/Sakhalin',
            'Kamtschatka' => 'Asia/Kamchatka', 'Riad' => 'Asia/Riyadh', 'Khartum' => 'Africa/Khartoum',
            'Singapur' => 'Asia/Singapore', 'St. Helena' => 'Atlantic/St_Helena', 'Mogadischu' => 'Africa/Mogadishu',
            'São Tomé' => 'Africa/Sao_Tome', 'Salvador' => 'America/El_Salvador', 'Damaskus' => 'Asia/Damascus',
            'Duschanbe' => 'Asia/Dushanbe', 'Port-of-Spain' => 'America/Port_of_Spain', 'Taipeh' => 'Asia/Taipei',
            'Daressalam' => 'Africa/Dar_es_Salaam', 'Uschgorod' => 'Europe/Uzhgorod', 'Kiew' => 'Europe/Kiev',
            'Saporischja' => 'Europe/Zaporozhye', 'Taschkent' => 'Asia/Tashkent', 'Wolgograd' => 'Europe/Volgograd',
            'St. Vincent' => 'America/St_Vincent', 'St. Thomas' => 'America/St_Thomas', 'Ürümqi' => 'Asia/Urumqi',
            'Monticello, Kentucky' => 'America/Kentucky/Monticello', 'Vevay, Indiana' => 'America/Indiana/Vevay',
            'Marengo, Indiana' => 'America/Indiana/Marengo', 'Winamac, Indiana' => 'America/Indiana/Winamac',
            'Tell City, Indiana' => 'America/Indiana/Tell_City', 'Petersburg, Indiana' => 'America/Indiana/Petersburg',
            'Vincennes, Indiana' => 'America/Indiana/Vincennes', 'Center, North Dakota' => 'America/North_Dakota/Center',
            'New Salem, North Dakota' => 'America/North_Dakota/New_Salem', 'Knox' => 'America/Indiana/Knox');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'timezonetocity', 'Fidschi');
        $this->assertEquals("Pacific/Fiji", $value);
    }

    /**
     * test for reading territorytophone from locale
     * expected array
     */
    public function testTerritoryToPhone()
    {
        $value = Cldr::getList('de_AT', 'territorytophone');
        $result = array('388' => '001', '247' => 'AC', '376' => 'AD', '971' => 'AE', '93' => 'AF',
            '1' => 'AG AI AS BB BM BS CA DM DO GD GU JM KN KY LC MP MS PR TC TT US VC VG VI', '355' => 'AL',
            '374' => 'AM', '599' => 'AN', '244' => 'AO', '672' => 'AQ NF', '54' => 'AR', '43' => 'AT',
            '61' => 'AU CC CX', '297' => 'AW', '358' => 'AX FI', '994' => 'AZ', '387' => 'BA', '880' => 'BD',
            '32' => 'BE', '226' => 'BF', '359' => 'BG', '973' => 'BH', '257' => 'BI', '229' => 'BJ',
            '590' => 'BL GP', '673' => 'BN', '591' => 'BO', '55' => 'BR', '975' => 'BT', '267' => 'BW',
            '375' => 'BY', '501' => 'BZ', '243' => 'CD', '236' => 'CF', '242' => 'CG', '41' => 'CH',
            '225' => 'CI', '682' => 'CK', '56' => 'CL', '237' => 'CM', '86' => 'CN', '57' => 'CO',
            '506' => 'CR', '53' => 'CU', '238' => 'CV', '357' => 'CY', '420' => 'CZ', '49' => 'DE',
            '253' => 'DJ', '45' => 'DK', '213' => 'DZ', '593' => 'EC', '372' => 'EE', '20' => 'EG',
            '291' => 'ER', '34' => 'ES', '251' => 'ET', '679' => 'FJ', '500' => 'FK', '691' => 'FM',
            '298' => 'FO', '33' => 'FR', '241' => 'GA', '44' => 'GB GG IM JE', '995' => 'GE', '594' => 'GF',
            '233' => 'GH', '350' => 'GI', '299' => 'GL', '220' => 'GM', '224' => 'GN', '240' => 'GQ',
            '30' => 'GR', '502' => 'GT', '245' => 'GW', '592' => 'GY', '852' => 'HK', '504' => 'HN',
            '385' => 'HR', '509' => 'HT', '36' => 'HU', '62' => 'ID', '353' => 'IE', '972' => 'IL PS',
            '91' => 'IN', '246' => 'IO', '964' => 'IQ', '98' => 'IR', '354' => 'IS', '39' => 'IT VA',
            '962' => 'JO', '81' => 'JP', '254' => 'KE', '996' => 'KG', '855' => 'KH', '686' => 'KI',
            '269' => 'KM', '850' => 'KP', '82' => 'KR', '965' => 'KW', '7' => 'KZ RU', '856' => 'LA',
            '961' => 'LB', '423' => 'LI', '94' => 'LK', '231' => 'LR', '266' => 'LS', '370' => 'LT',
            '352' => 'LU', '371' => 'LV', '218' => 'LY', '212' => 'MA', '377' => 'MC', '373' => 'MD',
            '382' => 'ME', '261' => 'MG', '692' => 'MH', '389' => 'MK', '223' => 'ML', '95' => 'MM',
            '976' => 'MN', '853' => 'MO', '596' => 'MQ', '222' => 'MR', '356' => 'MT', '230' => 'MU',
            '960' => 'MV', '265' => 'MW', '52' => 'MX', '60' => 'MY', '258' => 'MZ', '264' => 'NA',
            '687' => 'NC', '227' => 'NE', '234' => 'NG', '505' => 'NI', '31' => 'NL', '47' => 'NO SJ',
            '977' => 'NP', '674' => 'NR', '683' => 'NU', '64' => 'NZ', '968' => 'OM', '507' => 'PA',
            '51' => 'PE', '689' => 'PF', '675' => 'PG', '63' => 'PH', '92' => 'PK', '48' => 'PL',
            '508' => 'PM', '351' => 'PT', '680' => 'PW', '595' => 'PY', '974' => 'QA', '262' => 'RE TF YT',
            '40' => 'RO', '381' => 'RS', '250' => 'RW', '966' => 'SA', '677' => 'SB', '248' => 'SC',
            '249' => 'SD', '46' => 'SE', '65' => 'SG', '290' => 'SH', '386' => 'SI', '421' => 'SK',
            '232' => 'SL', '378' => 'SM', '221' => 'SN', '252' => 'SO', '597' => 'SR', '239' => 'ST',
            '503' => 'SV', '963' => 'SY', '268' => 'SZ', '235' => 'TD', '228' => 'TG', '66' => 'TH',
            '992' => 'TJ', '690' => 'TK', '670' => 'TL', '993' => 'TM', '216' => 'TN', '676' => 'TO',
            '90' => 'TR', '688' => 'TV', '886' => 'TW', '255' => 'TZ', '380' => 'UA', '256' => 'UG',
            '598' => 'UY', '998' => 'UZ', '58' => 'VE', '84' => 'VN', '678' => 'VU', '681' => 'WF',
            '685' => 'WS', '967' => 'YE', '27' => 'ZA', '260' => 'ZM', '263' => 'ZW');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'territorytophone', '43');
        $this->assertEquals("AT", $value);
    }

    /**
     * test for reading phonetoterritory from locale
     * expected array
     */
    public function testPhoneToTerritory()
    {
        $value = Cldr::getList('de_AT', 'phonetoterritory');
        $result = array('001' => '388', 'AC' => '247', 'AD' => '376', 'AE' => '971', 'AF' => '93', 'AG' => '1',
            'AI' => '1', 'AL' => '355', 'AM' => '374', 'AN' => '599', 'AO' => '244', 'AQ' => '672',
            'AR' => '54', 'AS' => '1', 'AT' => '43', 'AU' => '61', 'AW' => '297', 'AX' => '358', 'AZ' => '994',
            'BA' => '387', 'BB' => '1', 'BD' => '880', 'BE' => '32', 'BF' => '226', 'BG' => '359',
            'BH' => '973', 'BI' => '257', 'BJ' => '229', 'BL' => '590', 'BM' => '1', 'BN' => '673',
            'BO' => '591', 'BR' => '55', 'BS' => '1', 'BT' => '975', 'BW' => '267', 'BY' => '375',
            'BZ' => '501', 'CA' => '1', 'CC' => '61', 'CD' => '243', 'CF' => '236', 'CG' => '242',
            'CH' => '41', 'CI' => '225', 'CK' => '682', 'CL' => '56', 'CM' => '237', 'CN' => '86',
            'CO' => '57', 'CR' => '506', 'CU' => '53', 'CV' => '238', 'CX' => '61', 'CY' => '357',
            'CZ' => '420', 'DE' => '49', 'DJ' => '253', 'DK' => '45', 'DM' => '1', 'DO' => '1', 'DZ' => '213',
            'EC' => '593', 'EE' => '372', 'EG' => '20', 'ER' => '291', 'ES' => '34', 'ET' => '251',
            'FI' => '358', 'FJ' => '679', 'FK' => '500', 'FM' => '691', 'FO' => '298', 'FR' => '33',
            'GA' => '241', 'GB' => '44', 'GD' => '1', 'GE' => '995', 'GF' => '594', 'GG' => '44',
            'GH' => '233', 'GI' => '350', 'GL' => '299', 'GM' => '220', 'GN' => '224', 'GP' => '590',
            'GQ' => '240', 'GR' => '30', 'GT' => '502', 'GU' => '1', 'GW' => '245', 'GY' => '592',
            'HK' => '852', 'HN' => '504', 'HR' => '385', 'HT' => '509', 'HU' => '36', 'ID' => '62',
            'IE' => '353', 'IL' => '972', 'IM' => '44', 'IN' => '91', 'IO' => '246', 'IQ' => '964',
            'IR' => '98', 'IS' => '354', 'IT' => '39', 'JE' => '44', 'JM' => '1', 'JO' => '962', 'JP' => '81',
            'KE' => '254', 'KG' => '996', 'KH' => '855', 'KI' => '686', 'KM' => '269', 'KN' => '1',
            'KP' => '850', 'KR' => '82', 'KW' => '965', 'KY' => '1', 'KZ' => '7', 'LA' => '856', 'LB' => '961',
            'LC' => '1', 'LI' => '423', 'LK' => '94', 'LR' => '231', 'LS' => '266', 'LT' => '370',
            'LU' => '352', 'LV' => '371', 'LY' => '218', 'MA' => '212', 'MC' => '377', 'MD' => '373',
            'ME' => '382', 'MG' => '261', 'MH' => '692', 'MK' => '389', 'ML' => '223', 'MM' => '95',
            'MN' => '976', 'MO' => '853', 'MP' => '1', 'MQ' => '596', 'MR' => '222', 'MS' => '1',
            'MT' => '356', 'MU' => '230', 'MV' => '960', 'MW' => '265', 'MX' => '52', 'MY' => '60',
            'MZ' => '258', 'NA' => '264', 'NC' => '687', 'NE' => '227', 'NF' => '672', 'NG' => '234',
            'NI' => '505', 'NL' => '31', 'NO' => '47', 'NP' => '977', 'NR' => '674', 'NU' => '683',
            'NZ' => '64', 'OM' => '968', 'PA' => '507', 'PE' => '51', 'PF' => '689', 'PG' => '675',
            'PH' => '63', 'PK' => '92', 'PL' => '48', 'PM' => '508', 'PR' => '1', 'PS' => '972', 'PT' => '351',
            'PW' => '680', 'PY' => '595', 'QA' => '974', 'RE' => '262', 'RO' => '40', 'RS' => '381',
            'RU' => '7', 'RW' => '250', 'SA' => '966', 'SB' => '677', 'SC' => '248', 'SD' => '249',
            'SE' => '46', 'SG' => '65', 'SH' => '290', 'SI' => '386', 'SJ' => '47', 'SK' => '421',
            'SL' => '232', 'SM' => '378', 'SN' => '221', 'SO' => '252', 'SR' => '597', 'ST' => '239',
            'SV' => '503', 'SY' => '963', 'SZ' => '268', 'TC' => '1', 'TD' => '235', 'TF' => '262',
            'TG' => '228', 'TH' => '66', 'TJ' => '992', 'TK' => '690', 'TL' => '670', 'TM' => '993',
            'TN' => '216', 'TO' => '676', 'TR' => '90', 'TT' => '1', 'TV' => '688', 'TW' => '886',
            'TZ' => '255', 'UA' => '380', 'UG' => '256', 'US' => '1', 'UY' => '598', 'UZ' => '998',
            'VA' => '39', 'VC' => '1', 'VE' => '58', 'VG' => '1', 'VI' => '1', 'VN' => '84', 'VU' => '678',
            'WF' => '681', 'WS' => '685', 'YE' => '967', 'YT' => '262', 'ZA' => '27', 'ZM' => '260',
            'ZW' => '263');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'phonetoterritory', 'AT');
        $this->assertEquals("43", $value);
    }

    /**
     * test for reading territorytonumeric from locale
     * expected array
     */
    public function testTerritoryToNumeric()
    {
        $value = Cldr::getList('de_AT', 'territorytonumeric');
        $result = array('958' => 'AA', '020' => 'AD', '784' => 'AE', '004' => 'AF', '028' => 'AG',
            '660' => 'AI', '008' => 'AL', '051' => 'AM', '530' => 'AN', '024' => 'AO', '010' => 'AQ',
            '032' => 'AR', '016' => 'AS', '040' => 'AT', '036' => 'AU', '533' => 'AW', '248' => 'AX',
            '031' => 'AZ', '070' => 'BA', '052' => 'BB', '050' => 'BD', '056' => 'BE', '854' => 'BF',
            '100' => 'BG', '048' => 'BH', '108' => 'BI', '204' => 'BJ', '652' => 'BL', '060' => 'BM',
            '096' => 'BN', '068' => 'BO', '076' => 'BR', '044' => 'BS', '064' => 'BT', '104' => 'BU',
            '074' => 'BV', '072' => 'BW', '112' => 'BY', '084' => 'BZ', '124' => 'CA', '166' => 'CC',
            '180' => 'CD', '140' => 'CF', '178' => 'CG', '756' => 'CH', '384' => 'CI', '184' => 'CK',
            '152' => 'CL', '120' => 'CM', '156' => 'CN', '170' => 'CO', '188' => 'CR', '891' => 'CS',
            '192' => 'CU', '132' => 'CV', '162' => 'CX', '196' => 'CY', '203' => 'CZ', '278' => 'DD',
            '276' => 'DE', '262' => 'DJ', '208' => 'DK', '212' => 'DM', '214' => 'DO', '012' => 'DZ',
            '218' => 'EC', '233' => 'EE', '818' => 'EG', '732' => 'EH', '232' => 'ER', '724' => 'ES',
            '231' => 'ET', '246' => 'FI', '242' => 'FJ', '238' => 'FK', '583' => 'FM', '234' => 'FO',
            '250' => 'FR', '249' => 'FX', '266' => 'GA', '826' => 'GB', '308' => 'GD', '268' => 'GE',
            '254' => 'GF', '831' => 'GG', '288' => 'GH', '292' => 'GI', '304' => 'GL', '270' => 'GM',
            '324' => 'GN', '312' => 'GP', '226' => 'GQ', '300' => 'GR', '239' => 'GS', '320' => 'GT',
            '316' => 'GU', '624' => 'GW', '328' => 'GY', '344' => 'HK', '334' => 'HM', '340' => 'HN',
            '191' => 'HR', '332' => 'HT', '348' => 'HU', '360' => 'ID', '372' => 'IE', '376' => 'IL',
            '833' => 'IM', '356' => 'IN', '086' => 'IO', '368' => 'IQ', '364' => 'IR', '352' => 'IS',
            '380' => 'IT', '832' => 'JE', '388' => 'JM', '400' => 'JO', '392' => 'JP', '404' => 'KE',
            '417' => 'KG', '116' => 'KH', '296' => 'KI', '174' => 'KM', '659' => 'KN', '408' => 'KP',
            '410' => 'KR', '414' => 'KW', '136' => 'KY', '398' => 'KZ', '418' => 'LA', '422' => 'LB',
            '662' => 'LC', '438' => 'LI', '144' => 'LK', '430' => 'LR', '426' => 'LS', '440' => 'LT',
            '442' => 'LU', '428' => 'LV', '434' => 'LY', '504' => 'MA', '492' => 'MC', '498' => 'MD',
            '499' => 'ME', '450' => 'MG', '663' => 'MF', '584' => 'MH', '807' => 'MK', '466' => 'ML',
            '496' => 'MN', '446' => 'MO', '580' => 'MP', '474' => 'MQ', '478' => 'MR', '500' => 'MS',
            '470' => 'MT', '480' => 'MU', '462' => 'MV', '454' => 'MW', '484' => 'MX', '458' => 'MY',
            '508' => 'MZ', '516' => 'NA', '540' => 'NC', '562' => 'NE', '574' => 'NF', '566' => 'NG',
            '558' => 'NI', '528' => 'NL', '578' => 'NO', '524' => 'NP', '520' => 'NR', '536' => 'NT',
            '570' => 'NU', '554' => 'NZ', '512' => 'OM', '591' => 'PA', '604' => 'PE', '258' => 'PF',
            '598' => 'PG', '608' => 'PH', '586' => 'PK', '616' => 'PL', '666' => 'PM', '612' => 'PN',
            '630' => 'PR', '275' => 'PS', '620' => 'PT', '585' => 'PW', '600' => 'PY', '634' => 'QA',
            '959' => 'QM', '960' => 'QN', '961' => 'QO', '962' => 'QP', '963' => 'QQ', '964' => 'QR',
            '965' => 'QS', '966' => 'QT', '967' => 'EU', '968' => 'QV', '969' => 'QW', '970' => 'QX',
            '971' => 'QY', '972' => 'QZ', '638' => 'RE', '642' => 'RO', '688' => 'RS', '643' => 'RU',
            '646' => 'RW', '682' => 'SA', '090' => 'SB', '690' => 'SC', '736' => 'SD', '752' => 'SE',
            '702' => 'SG', '654' => 'SH', '705' => 'SI', '744' => 'SJ', '703' => 'SK', '694' => 'SL',
            '674' => 'SM', '686' => 'SN', '706' => 'SO', '740' => 'SR', '678' => 'ST', '810' => 'SU',
            '222' => 'SV', '760' => 'SY', '748' => 'SZ', '796' => 'TC', '148' => 'TD', '260' => 'TF',
            '768' => 'TG', '764' => 'TH', '762' => 'TJ', '772' => 'TK', '626' => 'TL', '795' => 'TM',
            '788' => 'TN', '776' => 'TO', '792' => 'TR', '780' => 'TT', '798' => 'TV', '158' => 'TW',
            '834' => 'TZ', '804' => 'UA', '800' => 'UG', '581' => 'UM', '840' => 'US', '858' => 'UY',
            '860' => 'UZ', '336' => 'VA', '670' => 'VC', '862' => 'VE', '092' => 'VG', '850' => 'VI',
            '704' => 'VN', '548' => 'VU', '876' => 'WF', '882' => 'WS', '973' => 'XA', '974' => 'XB',
            '975' => 'XC', '976' => 'XD', '977' => 'XE', '978' => 'XF', '979' => 'XG', '980' => 'XH',
            '981' => 'XI', '982' => 'XJ', '983' => 'XK', '984' => 'XL', '985' => 'XM', '986' => 'XN',
            '987' => 'XO', '988' => 'XP', '989' => 'XQ', '990' => 'XR', '991' => 'XS', '992' => 'XT',
            '993' => 'XU', '994' => 'XV', '995' => 'XW', '996' => 'XX', '997' => 'XY', '998' => 'XZ',
            '720' => 'YD', '887' => 'YE', '175' => 'YT', '710' => 'ZA', '894' => 'ZM', '716' => 'ZW',
            '999' => 'ZZ');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'territorytonumeric', '040');
        $this->assertEquals("AT", $value);
    }

    /**
     * test for reading numerictoterritory from locale
     * expected array
     */
    public function testNumericToTerritory()
    {
        $value = Cldr::getList('de_AT', 'numerictoterritory');
        $result = array( 'AA' => '958', 'AD' => '020', 'AE' => '784', 'AF' => '004', 'AG' => '028',
            'AI' => '660', 'AL' => '008', 'AM' => '051', 'AN' => '530', 'AO' => '024', 'AQ' => '010',
            'AR' => '032', 'AS' => '016', 'AT' => '040', 'AU' => '036', 'AW' => '533', 'AX' => '248',
            'AZ' => '031', 'BA' => '070', 'BB' => '052', 'BD' => '050', 'BE' => '056', 'BF' => '854',
            'BG' => '100', 'BH' => '048', 'BI' => '108', 'BJ' => '204', 'BL' => '652', 'BM' => '060',
            'BN' => '096', 'BO' => '068', 'BR' => '076', 'BS' => '044', 'BT' => '064', 'BU' => '104',
            'BV' => '074', 'BW' => '072', 'BY' => '112', 'BZ' => '084', 'CA' => '124', 'CC' => '166',
            'CD' => '180', 'CF' => '140', 'CG' => '178', 'CH' => '756', 'CI' => '384', 'CK' => '184',
            'CL' => '152', 'CM' => '120', 'CN' => '156', 'CO' => '170', 'CR' => '188', 'CS' => '891',
            'CU' => '192', 'CV' => '132', 'CX' => '162', 'CY' => '196', 'CZ' => '203', 'DD' => '278',
            'DE' => '276', 'DJ' => '262', 'DK' => '208', 'DM' => '212', 'DO' => '214', 'DZ' => '012',
            'EC' => '218', 'EE' => '233', 'EG' => '818', 'EH' => '732', 'ER' => '232', 'ES' => '724',
            'ET' => '231', 'FI' => '246', 'FJ' => '242', 'FK' => '238', 'FM' => '583', 'FO' => '234',
            'FR' => '250', 'FX' => '249', 'GA' => '266', 'GB' => '826', 'GD' => '308', 'GE' => '268',
            'GF' => '254', 'GG' => '831', 'GH' => '288', 'GI' => '292', 'GL' => '304', 'GM' => '270',
            'GN' => '324', 'GP' => '312', 'GQ' => '226', 'GR' => '300', 'GS' => '239', 'GT' => '320',
            'GU' => '316', 'GW' => '624', 'GY' => '328', 'HK' => '344', 'HM' => '334', 'HN' => '340',
            'HR' => '191', 'HT' => '332', 'HU' => '348', 'ID' => '360', 'IE' => '372', 'IL' => '376',
            'IM' => '833', 'IN' => '356', 'IO' => '086', 'IQ' => '368', 'IR' => '364', 'IS' => '352',
            'IT' => '380', 'JE' => '832', 'JM' => '388', 'JO' => '400', 'JP' => '392', 'KE' => '404',
            'KG' => '417', 'KH' => '116', 'KI' => '296', 'KM' => '174', 'KN' => '659', 'KP' => '408',
            'KR' => '410', 'KW' => '414', 'KY' => '136', 'KZ' => '398', 'LA' => '418', 'LB' => '422',
            'LC' => '662', 'LI' => '438', 'LK' => '144', 'LR' => '430', 'LS' => '426', 'LT' => '440',
            'LU' => '442', 'LV' => '428', 'LY' => '434', 'MA' => '504', 'MC' => '492', 'MD' => '498',
            'ME' => '499', 'MG' => '450', 'MF' => '663', 'MH' => '584', 'MK' => '807', 'ML' => '466',
            'MM' => '104', 'MN' => '496', 'MO' => '446', 'MP' => '580', 'MQ' => '474', 'MR' => '478',
            'MS' => '500', 'MT' => '470', 'MU' => '480', 'MV' => '462', 'MW' => '454', 'MX' => '484',
            'MY' => '458', 'MZ' => '508', 'NA' => '516', 'NC' => '540', 'NE' => '562', 'NF' => '574',
            'NG' => '566', 'NI' => '558', 'NL' => '528', 'NO' => '578', 'NP' => '524', 'NR' => '520',
            'NT' => '536', 'NU' => '570', 'NZ' => '554', 'OM' => '512', 'PA' => '591', 'PE' => '604',
            'PF' => '258', 'PG' => '598', 'PH' => '608', 'PK' => '586', 'PL' => '616', 'PM' => '666',
            'PN' => '612', 'PR' => '630', 'PS' => '275', 'PT' => '620', 'PW' => '585', 'PY' => '600',
            'QA' => '634', 'QM' => '959', 'QN' => '960', 'QO' => '961', 'QP' => '962', 'QQ' => '963',
            'QR' => '964', 'QS' => '965', 'QT' => '966', 'EU' => '967', 'QV' => '968', 'QW' => '969',
            'QX' => '970', 'QY' => '971', 'QZ' => '972', 'RE' => '638', 'RO' => '642', 'RS' => '688',
            'RU' => '643', 'RW' => '646', 'SA' => '682', 'SB' => '090', 'SC' => '690', 'SD' => '736',
            'SE' => '752', 'SG' => '702', 'SH' => '654', 'SI' => '705', 'SJ' => '744', 'SK' => '703',
            'SL' => '694', 'SM' => '674', 'SN' => '686', 'SO' => '706', 'SR' => '740', 'ST' => '678',
            'SU' => '810', 'SV' => '222', 'SY' => '760', 'SZ' => '748', 'TC' => '796', 'TD' => '148',
            'TF' => '260', 'TG' => '768', 'TH' => '764', 'TJ' => '762', 'TK' => '772', 'TL' => '626',
            'TM' => '795', 'TN' => '788', 'TO' => '776', 'TP' => '626', 'TR' => '792', 'TT' => '780',
            'TV' => '798', 'TW' => '158', 'TZ' => '834', 'UA' => '804', 'UG' => '800', 'UM' => '581',
            'US' => '840', 'UY' => '858', 'UZ' => '860', 'VA' => '336', 'VC' => '670', 'VE' => '862',
            'VG' => '092', 'VI' => '850', 'VN' => '704', 'VU' => '548', 'WF' => '876', 'WS' => '882',
            'XA' => '973', 'XB' => '974', 'XC' => '975', 'XD' => '976', 'XE' => '977', 'XF' => '978',
            'XG' => '979', 'XH' => '980', 'XI' => '981', 'XJ' => '982', 'XK' => '983', 'XL' => '984',
            'XM' => '985', 'XN' => '986', 'XO' => '987', 'XP' => '988', 'XQ' => '989', 'XR' => '990',
            'XS' => '991', 'XT' => '992', 'XU' => '993', 'XV' => '994', 'XW' => '995', 'XX' => '996',
            'XY' => '997', 'XZ' => '998', 'YD' => '720', 'YE' => '887', 'YT' => '175', 'YU' => '891',
            'ZA' => '710', 'ZM' => '894', 'ZR' => '180', 'ZW' => '716', 'ZZ' => '999');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'numerictoterritory', 'AT');
        $this->assertEquals("040", $value);
    }

    /**
     * test for reading territorytonumeric from locale
     * expected array
     */
    public function testTerritoryToAlpha3()
    {
        $value = Cldr::getList('de_AT', 'territorytoalpha3');
        $result = array('AAA' => 'AA', 'AND' => 'AD', 'ARE' => 'AE', 'AFG' => 'AF', 'ATG' => 'AG',
            'AIA' => 'AI', 'ALB' => 'AL', 'ARM' => 'AM', 'ANT' => 'AN', 'AGO' => 'AO', 'ATA' => 'AQ',
            'ARG' => 'AR', 'ASM' => 'AS', 'AUT' => 'AT', 'AUS' => 'AU', 'ABW' => 'AW', 'ALA' => 'AX',
            'AZE' => 'AZ', 'BIH' => 'BA', 'BRB' => 'BB', 'BGD' => 'BD', 'BEL' => 'BE', 'BFA' => 'BF',
            'BGR' => 'BG', 'BHR' => 'BH', 'BDI' => 'BI', 'BEN' => 'BJ', 'BLM' => 'BL', 'BMU' => 'BM',
            'BRN' => 'BN', 'BOL' => 'BO', 'BRA' => 'BR', 'BHS' => 'BS', 'BTN' => 'BT', 'BUR' => 'BU',
            'BVT' => 'BV', 'BWA' => 'BW', 'BLR' => 'BY', 'BLZ' => 'BZ', 'CAN' => 'CA', 'CCK' => 'CC',
            'COD' => 'CD', 'CAF' => 'CF', 'COG' => 'CG', 'CHE' => 'CH', 'CIV' => 'CI', 'COK' => 'CK',
            'CHL' => 'CL', 'CMR' => 'CM', 'CHN' => 'CN', 'COL' => 'CO', 'CRI' => 'CR', 'SCG' => 'CS',
            'CUB' => 'CU', 'CPV' => 'CV', 'CXR' => 'CX', 'CYP' => 'CY', 'CZE' => 'CZ', 'DDR' => 'DD',
            'DEU' => 'DE', 'DJI' => 'DJ', 'DNK' => 'DK', 'DMA' => 'DM', 'DOM' => 'DO', 'DZA' => 'DZ',
            'ECU' => 'EC', 'EST' => 'EE', 'EGY' => 'EG', 'ESH' => 'EH', 'ERI' => 'ER', 'ESP' => 'ES',
            'ETH' => 'ET', 'FIN' => 'FI', 'FJI' => 'FJ', 'FLK' => 'FK', 'FSM' => 'FM', 'FRO' => 'FO',
            'FRA' => 'FR', 'FXX' => 'FX', 'GAB' => 'GA', 'GBR' => 'GB', 'GRD' => 'GD', 'GEO' => 'GE',
            'GUF' => 'GF', 'GGY' => 'GG', 'GHA' => 'GH', 'GIB' => 'GI', 'GRL' => 'GL', 'GMB' => 'GM',
            'GIN' => 'GN', 'GLP' => 'GP', 'GNQ' => 'GQ', 'GRC' => 'GR', 'SGS' => 'GS', 'GTM' => 'GT',
            'GUM' => 'GU', 'GNB' => 'GW', 'GUY' => 'GY', 'HKG' => 'HK', 'HMD' => 'HM', 'HND' => 'HN',
            'HRV' => 'HR', 'HTI' => 'HT', 'HUN' => 'HU', 'IDN' => 'ID', 'IRL' => 'IE', 'ISR' => 'IL',
            'IMN' => 'IM', 'IND' => 'IN', 'IOT' => 'IO', 'IRQ' => 'IQ', 'IRN' => 'IR', 'ISL' => 'IS',
            'ITA' => 'IT', 'JEY' => 'JE', 'JAM' => 'JM', 'JOR' => 'JO', 'JPN' => 'JP', 'KEN' => 'KE',
            'KGZ' => 'KG', 'KHM' => 'KH', 'KIR' => 'KI', 'COM' => 'KM', 'KNA' => 'KN', 'PRK' => 'KP',
            'KOR' => 'KR', 'KWT' => 'KW', 'CYM' => 'KY', 'KAZ' => 'KZ', 'LAO' => 'LA', 'LBN' => 'LB',
            'LCA' => 'LC', 'LIE' => 'LI', 'LKA' => 'LK', 'LBR' => 'LR', 'LSO' => 'LS', 'LTU' => 'LT',
            'LUX' => 'LU', 'LVA' => 'LV', 'LBY' => 'LY', 'MAR' => 'MA', 'MCO' => 'MC', 'MDA' => 'MD',
            'MNE' => 'ME', 'MDG' => 'MG', 'MAF' => 'MF', 'MHL' => 'MH', 'MKD' => 'MK', 'MLI' => 'ML',
            'MMR' => 'MM', 'MNG' => 'MN', 'MAC' => 'MO', 'MNP' => 'MP', 'MTQ' => 'MQ', 'MRT' => 'MR',
            'MSR' => 'MS', 'MLT' => 'MT', 'MUS' => 'MU', 'MDV' => 'MV', 'MWI' => 'MW', 'MEX' => 'MX',
            'MYS' => 'MY', 'MOZ' => 'MZ', 'NAM' => 'NA', 'NCL' => 'NC', 'NER' => 'NE', 'NFK' => 'NF',
            'NGA' => 'NG', 'NIC' => 'NI', 'NLD' => 'NL', 'NOR' => 'NO', 'NPL' => 'NP', 'NRU' => 'NR',
            'NTZ' => 'NT', 'NIU' => 'NU', 'NZL' => 'NZ', 'OMN' => 'OM', 'PAN' => 'PA', 'PER' => 'PE',
            'PYF' => 'PF', 'PNG' => 'PG', 'PHL' => 'PH', 'PAK' => 'PK', 'POL' => 'PL', 'SPM' => 'PM',
            'PCN' => 'PN', 'PRI' => 'PR', 'PSE' => 'PS', 'PRT' => 'PT', 'PLW' => 'PW', 'PRY' => 'PY',
            'QAT' => 'QA', 'QMM' => 'QM', 'QNN' => 'QN', 'QOO' => 'QO', 'QPP' => 'QP', 'QQQ' => 'QQ',
            'QRR' => 'QR', 'QSS' => 'QS', 'QTT' => 'QT', 'QUU' => 'EU', 'QVV' => 'QV', 'QWW' => 'QW',
            'QXX' => 'QX', 'QYY' => 'QY', 'QZZ' => 'QZ', 'REU' => 'RE', 'ROU' => 'RO', 'SRB' => 'RS',
            'RUS' => 'RU', 'RWA' => 'RW', 'SAU' => 'SA', 'SLB' => 'SB', 'SYC' => 'SC', 'SDN' => 'SD',
            'SWE' => 'SE', 'SGP' => 'SG', 'SHN' => 'SH', 'SVN' => 'SI', 'SJM' => 'SJ', 'SVK' => 'SK',
            'SLE' => 'SL', 'SMR' => 'SM', 'SEN' => 'SN', 'SOM' => 'SO', 'SUR' => 'SR', 'STP' => 'ST',
            'SUN' => 'SU', 'SLV' => 'SV', 'SYR' => 'SY', 'SWZ' => 'SZ', 'TCA' => 'TC', 'TCD' => 'TD',
            'ATF' => 'TF', 'TGO' => 'TG', 'THA' => 'TH', 'TJK' => 'TJ', 'TKL' => 'TK', 'TLS' => 'TL',
            'TKM' => 'TM', 'TUN' => 'TN', 'TON' => 'TO', 'TMP' => 'TP', 'TUR' => 'TR', 'TTO' => 'TT',
            'TUV' => 'TV', 'TWN' => 'TW', 'TZA' => 'TZ', 'UKR' => 'UA', 'UGA' => 'UG', 'UMI' => 'UM',
            'USA' => 'US', 'URY' => 'UY', 'UZB' => 'UZ', 'VAT' => 'VA', 'VCT' => 'VC', 'VEN' => 'VE',
            'VGB' => 'VG', 'VIR' => 'VI', 'VNM' => 'VN', 'VUT' => 'VU', 'WLF' => 'WF', 'WSM' => 'WS',
            'XAA' => 'XA', 'XBB' => 'XB', 'XCC' => 'XC', 'XDD' => 'XD', 'XEE' => 'XE', 'XFF' => 'XF',
            'XGG' => 'XG', 'XHH' => 'XH', 'XII' => 'XI', 'XJJ' => 'XJ', 'XKK' => 'XK', 'XLL' => 'XL',
            'XMM' => 'XM', 'XNN' => 'XN', 'XOO' => 'XO', 'XPP' => 'XP', 'XQQ' => 'XQ', 'XRR' => 'XR',
            'XSS' => 'XS', 'XTT' => 'XT', 'XUU' => 'XU', 'XVV' => 'XV', 'XWW' => 'XW', 'XXX' => 'XX',
            'XYY' => 'XY', 'XZZ' => 'XZ', 'YMD' => 'YD', 'YEM' => 'YE', 'MYT' => 'YT', 'YUG' => 'YU',
            'ZAF' => 'ZA', 'ZMB' => 'ZM', 'ZAR' => 'ZR', 'ZWE' => 'ZW', 'ZZZ' => 'ZZ');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'territorytoalpha3', 'AUT');
        $this->assertEquals("AT", $value);
    }

    /**
     * test for reading alpha3toterritory from locale
     * expected array
     */
    public function testAlpha3ToTerritory()
    {
        $value = Cldr::getList('de_AT', 'alpha3toterritory');
        $result = array('AA' => 'AAA', 'AD' => 'AND', 'AE' => 'ARE', 'AF' => 'AFG', 'AG' => 'ATG',
            'AI' => 'AIA', 'AL' => 'ALB', 'AM' => 'ARM', 'AN' => 'ANT', 'AO' => 'AGO', 'AQ' => 'ATA',
            'AR' => 'ARG', 'AS' => 'ASM', 'AT' => 'AUT', 'AU' => 'AUS', 'AW' => 'ABW', 'AX' => 'ALA',
            'AZ' => 'AZE', 'BA' => 'BIH', 'BB' => 'BRB', 'BD' => 'BGD', 'BE' => 'BEL', 'BF' => 'BFA',
            'BG' => 'BGR', 'BH' => 'BHR', 'BI' => 'BDI', 'BJ' => 'BEN', 'BL' => 'BLM', 'BM' => 'BMU',
            'BN' => 'BRN', 'BO' => 'BOL', 'BR' => 'BRA', 'BS' => 'BHS', 'BT' => 'BTN', 'BU' => 'BUR',
            'BV' => 'BVT', 'BW' => 'BWA', 'BY' => 'BLR', 'BZ' => 'BLZ', 'CA' => 'CAN', 'CC' => 'CCK',
            'CD' => 'COD', 'CF' => 'CAF', 'CG' => 'COG', 'CH' => 'CHE', 'CI' => 'CIV', 'CK' => 'COK',
            'CL' => 'CHL', 'CM' => 'CMR', 'CN' => 'CHN', 'CO' => 'COL', 'CR' => 'CRI', 'CS' => 'SCG',
            'CU' => 'CUB', 'CV' => 'CPV', 'CX' => 'CXR', 'CY' => 'CYP', 'CZ' => 'CZE', 'DD' => 'DDR',
            'DE' => 'DEU', 'DJ' => 'DJI', 'DK' => 'DNK', 'DM' => 'DMA', 'DO' => 'DOM', 'DZ' => 'DZA',
            'EC' => 'ECU', 'EE' => 'EST', 'EG' => 'EGY', 'EH' => 'ESH', 'ER' => 'ERI', 'ES' => 'ESP',
            'ET' => 'ETH', 'FI' => 'FIN', 'FJ' => 'FJI', 'FK' => 'FLK', 'FM' => 'FSM', 'FO' => 'FRO',
            'FR' => 'FRA', 'FX' => 'FXX', 'GA' => 'GAB', 'GB' => 'GBR', 'GD' => 'GRD', 'GE' => 'GEO',
            'GF' => 'GUF', 'GG' => 'GGY', 'GH' => 'GHA', 'GI' => 'GIB', 'GL' => 'GRL', 'GM' => 'GMB',
            'GN' => 'GIN', 'GP' => 'GLP', 'GQ' => 'GNQ', 'GR' => 'GRC', 'GS' => 'SGS', 'GT' => 'GTM',
            'GU' => 'GUM', 'GW' => 'GNB', 'GY' => 'GUY', 'HK' => 'HKG', 'HM' => 'HMD', 'HN' => 'HND',
            'HR' => 'HRV', 'HT' => 'HTI', 'HU' => 'HUN', 'ID' => 'IDN', 'IE' => 'IRL', 'IL' => 'ISR',
            'IM' => 'IMN', 'IN' => 'IND', 'IO' => 'IOT', 'IQ' => 'IRQ', 'IR' => 'IRN', 'IS' => 'ISL',
            'IT' => 'ITA', 'JE' => 'JEY', 'JM' => 'JAM', 'JO' => 'JOR', 'JP' => 'JPN', 'KE' => 'KEN',
            'KG' => 'KGZ', 'KH' => 'KHM', 'KI' => 'KIR', 'KM' => 'COM', 'KN' => 'KNA', 'KP' => 'PRK',
            'KR' => 'KOR', 'KW' => 'KWT', 'KY' => 'CYM', 'KZ' => 'KAZ', 'LA' => 'LAO', 'LB' => 'LBN',
            'LC' => 'LCA', 'LI' => 'LIE', 'LK' => 'LKA', 'LR' => 'LBR', 'LS' => 'LSO', 'LT' => 'LTU',
            'LU' => 'LUX', 'LV' => 'LVA', 'LY' => 'LBY', 'MA' => 'MAR', 'MC' => 'MCO', 'MD' => 'MDA',
            'ME' => 'MNE', 'MG' => 'MDG', 'MF' => 'MAF', 'MH' => 'MHL', 'MK' => 'MKD', 'ML' => 'MLI',
            'MM' => 'MMR', 'MN' => 'MNG', 'MO' => 'MAC', 'MP' => 'MNP', 'MQ' => 'MTQ', 'MR' => 'MRT',
            'MS' => 'MSR', 'MT' => 'MLT', 'MU' => 'MUS', 'MV' => 'MDV', 'MW' => 'MWI', 'MX' => 'MEX',
            'MY' => 'MYS', 'MZ' => 'MOZ', 'NA' => 'NAM', 'NC' => 'NCL', 'NE' => 'NER', 'NF' => 'NFK',
            'NG' => 'NGA', 'NI' => 'NIC', 'NL' => 'NLD', 'NO' => 'NOR', 'NP' => 'NPL', 'NR' => 'NRU',
            'NT' => 'NTZ', 'NU' => 'NIU', 'NZ' => 'NZL', 'OM' => 'OMN', 'PA' => 'PAN', 'PE' => 'PER',
            'PF' => 'PYF', 'PG' => 'PNG', 'PH' => 'PHL', 'PK' => 'PAK', 'PL' => 'POL', 'PM' => 'SPM',
            'PN' => 'PCN', 'PR' => 'PRI', 'PS' => 'PSE', 'PT' => 'PRT', 'PW' => 'PLW', 'PY' => 'PRY',
            'QA' => 'QAT', 'QM' => 'QMM', 'QN' => 'QNN', 'QO' => 'QOO', 'QP' => 'QPP', 'QQ' => 'QQQ',
            'QR' => 'QRR', 'QS' => 'QSS', 'QT' => 'QTT', 'EU' => 'QUU', 'QV' => 'QVV', 'QW' => 'QWW',
            'QX' => 'QXX', 'QY' => 'QYY', 'QZ' => 'QZZ', 'RE' => 'REU', 'RO' => 'ROU', 'RS' => 'SRB',
            'RU' => 'RUS', 'RW' => 'RWA', 'SA' => 'SAU', 'SB' => 'SLB', 'SC' => 'SYC', 'SD' => 'SDN',
            'SE' => 'SWE', 'SG' => 'SGP', 'SH' => 'SHN', 'SI' => 'SVN', 'SJ' => 'SJM', 'SK' => 'SVK',
            'SL' => 'SLE', 'SM' => 'SMR', 'SN' => 'SEN', 'SO' => 'SOM', 'SR' => 'SUR', 'ST' => 'STP',
            'SU' => 'SUN', 'SV' => 'SLV', 'SY' => 'SYR', 'SZ' => 'SWZ', 'TC' => 'TCA', 'TD' => 'TCD',
            'TF' => 'ATF', 'TG' => 'TGO', 'TH' => 'THA', 'TJ' => 'TJK', 'TK' => 'TKL', 'TL' => 'TLS',
            'TM' => 'TKM', 'TN' => 'TUN', 'TO' => 'TON', 'TP' => 'TMP', 'TR' => 'TUR', 'TT' => 'TTO',
            'TV' => 'TUV', 'TW' => 'TWN', 'TZ' => 'TZA', 'UA' => 'UKR', 'UG' => 'UGA', 'UM' => 'UMI',
            'US' => 'USA', 'UY' => 'URY', 'UZ' => 'UZB', 'VA' => 'VAT', 'VC' => 'VCT', 'VE' => 'VEN',
            'VG' => 'VGB', 'VI' => 'VIR', 'VN' => 'VNM', 'VU' => 'VUT', 'WF' => 'WLF', 'WS' => 'WSM',
            'XA' => 'XAA', 'XB' => 'XBB', 'XC' => 'XCC', 'XD' => 'XDD', 'XE' => 'XEE', 'XF' => 'XFF',
            'XG' => 'XGG', 'XH' => 'XHH', 'XI' => 'XII', 'XJ' => 'XJJ', 'XK' => 'XKK', 'XL' => 'XLL',
            'XM' => 'XMM', 'XN' => 'XNN', 'XO' => 'XOO', 'XP' => 'XPP', 'XQ' => 'XQQ', 'XR' => 'XRR',
            'XS' => 'XSS', 'XT' => 'XTT', 'XU' => 'XUU', 'XV' => 'XVV', 'XW' => 'XWW', 'XX' => 'XXX',
            'XY' => 'XYY', 'XZ' => 'XZZ', 'YD' => 'YMD', 'YE' => 'YEM', 'YT' => 'MYT', 'YU' => 'YUG',
            'ZA' => 'ZAF', 'ZM' => 'ZMB', 'ZR' => 'ZAR', 'ZW' => 'ZWE', 'ZZ' => 'ZZZ');
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'alpha3toterritory', 'AT');
        $this->assertEquals("AUT", $value);
    }

    /**
     * test for reading postaltoterritory from locale
     * expected array
     */
    public function testPostalToTerritory()
    {
        $value = Cldr::getList('de_AT', 'postaltoterritory');
        $result = array('GB' => 'GIR[ ]?0AA|((AB|AL|B|BA|BB|BD|BH|BL|BN|BR|BS|BT|CA|CB|CF|CH|CM|CO|CR|CT|CV|CW|DA|DD|DE|DG|DH|DL|DN|DT|DY|E|EC|EH|EN|EX|FK|FY|G|GL|GY|GU|HA|HD|HG|HP|HR|HS|HU|HX|IG|IM|IP|IV|JE|KA|KT|KW|KY|L|LA|LD|LE|LL|LN|LS|LU|M|ME|MK|ML|N|NE|NG|NN|NP|NR|NW|OL|OX|PA|PE|PH|PL|PO|PR|RG|RH|RM|S|SA|SE|SG|SK|SL|SM|SN|SO|SP|SR|SS|ST|SW|SY|TA|TD|TF|TN|TQ|TR|TS|TW|UB|W|WA|WC|WD|WF|WN|WR|WS|WV|YO|ZE)(\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}))|BFPO[ ]?\d{1,4}',
            'JE' => 'JE\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}',
            'GG' => 'GY\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}',
            'IM' => 'IM\d[\dA-Z]?[ ]?\d[ABD-HJLN-UW-Z]{2}',
            'US' => '\d{5}([ \-]\d{4})?',
            'CA' => '[ABCEGHJKLMNPRSTVXY]\d[ABCEGHJ-NPRSTV-Z][ ]?\d[ABCEGHJ-NPRSTV-Z]\d',
            'DE' => '\d{5}',
            'JP' => '\d{3}-\d{4}',
            'FR' => '\d{2}[ ]?\d{3}',
            'AU' => '\d{4}',
            'IT' => '\d{5}',
            'CH' => '\d{4}',
            'AT' => '\d{4}',
            'ES' => '\d{5}',
            'NL' => '\d{4}[ ]?[A-Z]{2}',
            'BE' => '\d{4}',
            'DK' => '\d{4}',
            'SE' => '\d{3}[ ]?\d{2}',
            'NO' => '\d{4}',
            'BR' => '\d{5}[\-]?\d{3}',
            'PT' => '\d{4}([\-]\d{3})?',
            'FI' => '\d{5}',
            'AX' => '22\d{3}',
            'KR' => '\d{3}[\-]\d{3}',
            'CN' => '\d{6}',
            'TW' => '\d{3}(\d{2})?',
            'SG' => '\d{6}',
            'DZ' => '\d{5}',
            'AD' => 'AD\d{3}',
            'AR' => '([A-HJ-NP-Z])?\d{4}([A-Z]{3})?',
            'AM' => '(37)?\d{4}',
            'AZ' => '\d{4}',
            'BH' => '((1[0-2]|[2-9])\d{2})?',
            'BD' => '\d{4}',
            'BB' => '(BB\d{5})?',
            'BY' => '\d{6}',
            'BM' => '[A-Z]{2}[ ]?[A-Z0-9]{2}',
            'BA' => '\d{5}',
            'IO' => 'BBND 1ZZ',
            'BN' => '[A-Z]{2}[ ]?\d{4}',
            'BG' => '\d{4}',
            'KH' => '\d{5}',
            'CV' => '\d{4}',
            'CL' => '\d{7}',
            'CR' => '\d{4,5}|\d{3}-\d{4}',
            'HR' => '\d{5}',
            'CY' => '\d{4}',
            'CZ' => '\d{3}[ ]?\d{2}',
            'DO' => '\d{5}',
            'EC' => '([A-Z]\d{4}[A-Z]|(?:[A-Z]{2})?\d{6})?',
            'EG' => '\d{5}',
            'EE' => '\d{5}',
            'FO' => '\d{3}',
            'GE' => '\d{4}',
            'GR' => '\d{3}[ ]?\d{2}',
            'GL' => '39\d{2}',
            'GT' => '\d{5}',
            'HT' => '\d{4}',
            'HN' => '(?:\d{5})?',
            'HU' => '\d{4}',
            'IS' => '\d{3}',
            'IN' => '\d{6}',
            'ID' => '\d{5}',
            'IE' => '((D|DUBLIN)?([1-9]|6[wW]|1[0-8]|2[024]))?',
            'IL' => '\d{5}',
            'JO' => '\d{5}',
            'KZ' => '\d{6}',
            'KE' => '\d{5}',
            'KW' => '\d{5}',
            'LA' => '\d{5}',
            'LV' => '\d{4}',
            'LB' => '(\d{4}([ ]?\d{4})?)?',
            'LI' => '(948[5-9])|(949[0-7])',
            'LT' => '\d{5}',
            'LU' => '\d{4}',
            'MK' => '\d{4}',
            'MY' => '\d{5}',
            'MV' => '\d{5}',
            'MT' => '[A-Z]{3}[ ]?\d{2,4}',
            'MU' => '(\d{3}[A-Z]{2}\d{3})?',
            'MX' => '\d{5}',
            'MD' => '\d{4}',
            'MC' => '980\d{2}',
            'MA' => '\d{5}',
            'NP' => '\d{5}',
            'NZ' => '\d{4}',
            'NI' => '((\d{4}-)?\d{3}-\d{3}(-\d{1})?)?',
            'NG' => '(\d{6})?',
            'OM' => '(PC )?\d{3}',
            'PK' => '\d{5}',
            'PY' => '\d{4}',
            'PH' => '\d{4}',
            'PL' => '\d{2}-\d{3}',
            'PR' => '00[679]\d{2}([ \-]\d{4})?',
            'RO' => '\d{6}',
            'RU' => '\d{6}',
            'SM' => '4789\d',
            'SA' => '\d{5}',
            'SN' => '\d{5}',
            'SK' => '\d{3}[ ]?\d{2}',
            'SI' => '\d{4}',
            'ZA' => '\d{4}',
            'LK' => '\d{5}',
            'TJ' => '\d{6}',
            'TH' => '\d{5}',
            'TN' => '\d{4}',
            'TR' => '\d{5}',
            'TM' => '\d{6}',
            'UA' => '\d{5}',
            'UY' => '\d{5}',
            'UZ' => '\d{6}',
            'VA' => '00120',
            'VE' => '\d{4}',
            'ZM' => '\d{5}',
            'AS' => '96799',
            'CC' => '6799',
            'CK' => '\d{4}',
            'RS' => '\d{6}',
            'ME' => '8\d{4}',
            'CS' => '\d{5}',
            'YU' => '\d{5}',
            'CX' => '6798',
            'ET' => '\d{4}',
            'FK' => 'FIQQ 1ZZ',
            'NF' => '2899',
            'FM' => '(9694[1-4])([ \-]\d{4})?',
            'GF' => '9[78]3\d{2}',
            'GN' => '\d{3}',
            'GP' => '9[78][01]\d{2}',
            'GS' => 'SIQQ 1ZZ',
            'GU' => '969[123]\d([ \-]\d{4})?',
            'GW' => '\d{4}',
            'HM' => '\d{4}',
            'IQ' => '\d{5}',
            'KG' => '\d{6}',
            'LR' => '\d{4}',
            'LS' => '\d{3}',
            'MG' => '\d{3}',
            'MH' => '969[67]\d([ \-]\d{4})?',
            'MN' => '\d{6}',
            'MP' => '9695[012]([ \-]\d{4})?',
            'MQ' => '9[78]2\d{2}',
            'NC' => '988\d{2}',
            'NE' => '\d{4}',
            'VI' => '008(([0-4]\d)|(5[01]))([ \-]\d{4})?',
            'PF' => '987\d{2}',
            'PG' => '\d{3}',
            'PM' => '9[78]5\d{2}',
            'PN' => 'PCRN 1ZZ',
            'PW' => '96940',
            'RE' => '9[78]4\d{2}',
            'SH' => '(ASCN|STHL) 1ZZ',
            'SJ' => '\d{4}',
            'SO' => '\d{5}',
            'SZ' => '[HLMS]\d{3}',
            'TC' => 'TKCA 1ZZ',
            'WF' => '986\d{2}',
            'YT' => '976\d{2}'
        );
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'postaltoterritory', 'AT');
        $this->assertEquals('\d{4}', $value);
    }

    /**
     * test for reading numberingsystem from locale
     * expected array
     */
    public function testNumberingSystem()
    {
        $value = Cldr::getList('de_AT', 'numberingsystem');
        $result = array(
            'arab' => '٠١٢٣٤٥٦٧٨٩',
            'arabext' => '۰۱۲۳۴۵۶۷۸۹',
            'beng' => '০১২৩৪৫৬৭৮৯',
            'deva' => '०१२३४५६७८९',
            'fullwide' => '０１２３４５６７８９',
            'gujr' => '૦૧૨૩૪૫૬૭૮૯',
            'guru' => '੦੧੨੩੪੫੬੭੮੯',
            'hanidec' => '〇一二三四五六七八九',
            'khmr' => "០១២៣៤៥៦៧៨៩",
            'knda' => '೦೧೨೩೪೫೬೭೮೯',
            'laoo' => '໐໑໒໓໔໕໖໗໘໙',
            'latn' => '0123456789',
            'mlym' => '൦൧൨൩൪൫൬൭൮൯',
            'mong' => "᠐᠑᠒᠓᠔᠕᠖᠗᠘᠙",
            'mymr' => "၀၁၂၃၄၅၆၇၈၉",
            'orya' => '୦୧୨୩୪୫୬୭୮୯',
            'telu' => '౦౧౨౩౪౫౬౭౮౯',
            'thai' => '๐๑๒๓๔๕๖๗๘๙',
            'tibt' => '༠༡༢༣༤༥༦༧༨༩'
        );
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'numberingsystem', 'Arab');
        $this->assertEquals("٠١٢٣٤٥٦٧٨٩", $value);
    }

    /**
     * test for reading chartofallback from locale
     * expected array
     */
    public function testCharToFallback()
    {
        $value = Cldr::getList('de_AT', 'chartofallback');
        $this->assertEquals('©', $value['(C)']);
        $this->assertEquals('½', $value[' 1/2']);
        $this->assertEquals('Æ', $value['AE']);

        $value = Cldr::getContent('de_AT', 'chartofallback', '(C)');
        $this->assertEquals("©", $value);
    }

    /**
     * test for reading chartofallback from locale
     * expected array
     */
    public function testFallbackToChar()
    {
        $value = Cldr::getList('de_AT', 'fallbacktochar');
        $this->assertEquals('(C)', $value['©']);
        $this->assertEquals(' 1/2', $value['½']);
        $this->assertEquals('AE', $value['Æ']);

        $value = Cldr::getContent('de_AT', 'fallbacktochar', '©');
        $this->assertEquals('(C)', $value);
    }

    /**
     * test for reading chartofallback from locale
     * expected array
     */
    public function testLocaleUpgrade()
    {
        $value = Cldr::getList('de_AT', 'localeupgrade');
        $this->assertEquals('en_Latn_US', $value['en']);
        $this->assertEquals('de_Latn_DE', $value['de']);
        $this->assertEquals('sk_Latn_SK', $value['sk']);

        $value = Cldr::getContent('de_AT', 'localeupgrade', 'de');
        $this->assertEquals('de_Latn_DE', $value);
    }

    /**
     * test for reading datetime from locale
     * expected array
     */
    public function testDateItem()
    {
        $value = Cldr::getList('de_AT', 'dateitem');
        $result = array(
            'EEEd' => 'd EEE', 'Ed' => 'E, d.', 'H' => "HH 'Uhr'",
            'Hm' => 'HH:mm', 'M' => 'L', 'MEd' => 'E, d.M.',
            'MMM' => 'LLL', 'MMMEd' => 'E, d. MMM', 'MMMMEd' => 'E, d. MMMM',
            'MMMMdd' => 'dd. MMMM', 'MMMd' => 'd. MMM',
            'MMd' => 'd.MM.', 'MMdd' => 'dd.MM.', 'Md' => 'd.M.', 'd' => 'd',
            'ms' => 'mm:ss', 'y' => 'y', 'yM' => 'M.y',
            'yMEd' => 'EEE, d.M.y', 'yMMM' => 'MMM y', 'yMMMEd' => 'EEE, d. MMM y',
            'yQ' => 'Q y', 'yQQQ' => 'QQQ y',
            'yyMM' => 'MM.yy', 'yyMMM' => 'MMM yy', 'yyMMdd' => 'dd.MM.yy',
            'yyQ' => 'Q yy', 'yyQQQQ' => 'QQQQ yy', 'yyyy' => 'y',
            'yyyyMMMM' => 'MMMM y', 'Hms' => 'HH:mm:ss', 'hm' => 'h:mm a',
            'hms' => 'h:mm:ss a', 'h' => 'h a'
        );
        $this->assertEquals($result, $value);

        $value = Cldr::getList('de_AT', 'dateitem', 'gregorian');
        $result = array(
            'EEEd' => 'd EEE', 'Ed' => 'E, d.', 'H' => "HH 'Uhr'",
            'Hm' => 'HH:mm', 'M' => 'L', 'MEd' => 'E, d.M.',
            'MMM' => 'LLL', 'MMMEd' => 'E, d. MMM', 'MMMMEd' => 'E, d. MMMM',
            'MMMMdd' => 'dd. MMMM', 'MMMd' => 'd. MMM',
            'MMd' => 'd.MM.', 'MMdd' => 'dd.MM.', 'Md' => 'd.M.', 'd' => 'd',
            'ms' => 'mm:ss', 'y' => 'y', 'yM' => 'M.y',
            'yMEd' => 'EEE, d.M.y', 'yMMM' => 'MMM y', 'yMMMEd' => 'EEE, d. MMM y',
            'yQ' => 'Q y', 'yQQQ' => 'QQQ y',
            'yyMM' => 'MM.yy', 'yyMMM' => 'MMM yy', 'yyMMdd' => 'dd.MM.yy',
            'yyQ' => 'Q yy', 'yyQQQQ' => 'QQQQ yy', 'yyyy' => 'y',
            'yyyyMMMM' => 'MMMM y', 'Hms' => 'HH:mm:ss', 'hm' => 'h:mm a',
            'hms' => 'h:mm:ss a', 'h' => 'h a'
        );
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'dateitem', 'MMMEd');
        $this->assertEquals("E, d. MMM", $value);
    }

    /**
     * test for reading intervalformat from locale
     * expected array
     */
    public function testDateInterval()
    {
        $value = Cldr::getList('de_AT', 'dateinterval');
        $result = array(
            'M' => array('M' => 'M.-M.'),
            'MEd' => array(
                'd' => 'E, dd.MM. - E, dd.MM.',
                'M' => 'E, dd.MM. - E, dd.MM.'),
            'MMM' => array('M' => 'MMM-MMM'),
            'MMMEd' => array(
                'd' => 'E, dd. - E, dd. MMM',
                'M' => 'E, dd. MMM - E, dd. MMM'),
            'MMMM' => array('M' => 'LLLL-LLLL'),
            'MMMd' => array(
                'd' => 'dd.-dd. MMM',
                'M' => 'dd. MMM - dd. MMM'),
            'Md' => array(
                'd' => 'dd.MM. - dd.MM.',
                'M' => 'dd.MM. - dd.MM.'),
            'd' => array('d' => 'd.-d.'),
            'h' => array(
                'a' => 'h a - h a',
                'h' => 'h-h a'),
            'H' => array(
                'a' => "HH-HH 'Uhr'",
                'H' => "HH-HH 'Uhr'"),
            'hm' => array(
                'a' => 'h:mm a - h:mm a',
                'h' => 'h:mm-h:mm a',
                'm' => 'h:mm-h:mm a'),
            'Hm' => array(
                'a' => 'HH:mm-HH:mm',
                'H' => 'HH:mm-HH:mm',
                'm' => 'HH:mm-HH:mm'),
            'hmv' => array(
                'a' => 'h:mm a - h:mm a v',
                'h' => 'h:mm-h:mm a v',
                'm' => 'h:mm-h:mm a v'),
            'Hmv' => array(
                'a' => 'HH:mm-HH:mm v',
                'H' => 'HH:mm-HH:mm v',
                'm' => 'HH:mm-HH:mm v'),
            'hv' => array(
                'a' => 'h a - h a v',
                'h' => 'h-h a v'),
            'Hv' => array(
                'a' => "HH-HH 'Uhr' v",
                'H' => "HH-HH 'Uhr' v"),
            'y' => array('y' => 'y-y'),
            'yM' => array(
                'M' => 'MM.yy - MM.yy',
                'y' => 'MM.yy - MM.yy'),
            'yMEd' => array(
                'd' => 'E, dd.MM.yy - E, dd.MM.yy',
                'M' => 'E, dd.MM.yy - E, dd.MM.yy',
                'y' => 'E, dd.MM.yy - E, dd.MM.yy'),
            'yMMM' => array(
                'M' => 'MMM-MMM y',
                'y' => 'MMM y - MMM y'),
            'yMMMEd' => array(
                'd' => 'E, dd. - E, dd. MMM y',
                'M' => 'E, dd. MMM - E, dd. MMM y',
                'y' => 'E, dd. MMM y - E, dd. MMM y'),
            'yMMMM' => array(
                'M' => 'MMMM-MMMM y',
                'y' => 'MMMM y - MMMM y'),
            'yMMMd' => array(
                'd' => 'dd.-dd. MMM y',
                'M' => 'dd. MMM - dd. MMM y',
                'y' => 'dd. MMM y - dd. MMM y'),
            'yMd' => array(
                'd' => 'dd.MM.yy - dd.MM.yy',
                'M' => 'dd.MM.yy - dd.MM.yy',
                'y' => 'dd.MM.yy - dd.MM.yy')
        );
        $this->assertEquals($result, $value);

        $value = Cldr::getList('de_AT', 'dateinterval', 'gregorian');
        $result = array(
            'M' => array('M' => 'M.-M.'),
            'MEd' => array(
                'd' => 'E, dd.MM. - E, dd.MM.',
                'M' => 'E, dd.MM. - E, dd.MM.'),
            'MMM' => array('M' => 'MMM-MMM'),
            'MMMEd' => array(
                'd' => 'E, dd. - E, dd. MMM',
                'M' => 'E, dd. MMM - E, dd. MMM'),
            'MMMM' => array('M' => 'LLLL-LLLL'),
            'MMMd' => array(
                'd' => 'dd.-dd. MMM',
                'M' => 'dd. MMM - dd. MMM'),
            'Md' => array(
                'd' => 'dd.MM. - dd.MM.',
                'M' => 'dd.MM. - dd.MM.'),
            'd' => array('d' => 'd.-d.'),
            'h' => array(
                'a' => 'h a - h a',
                'h' => 'h-h a'),
            'H' => array(
                'a' => "HH-HH 'Uhr'",
                'H' => "HH-HH 'Uhr'"),
            'hm' => array(
                'a' => 'h:mm a - h:mm a',
                'h' => 'h:mm-h:mm a',
                'm' => 'h:mm-h:mm a'),
            'Hm' => array(
                'a' => 'HH:mm-HH:mm',
                'H' => 'HH:mm-HH:mm',
                'm' => 'HH:mm-HH:mm'),
            'hmv' => array(
                'a' => 'h:mm a - h:mm a v',
                'h' => 'h:mm-h:mm a v',
                'm' => 'h:mm-h:mm a v'),
            'Hmv' => array(
                'a' => 'HH:mm-HH:mm v',
                'H' => 'HH:mm-HH:mm v',
                'm' => 'HH:mm-HH:mm v'),
            'hv' => array(
                'a' => 'h a - h a v',
                'h' => 'h-h a v'),
            'Hv' => array(
                'a' => "HH-HH 'Uhr' v",
                'H' => "HH-HH 'Uhr' v"),
            'y' => array('y' => 'y-y'),
            'yM' => array(
                'M' => 'MM.yy - MM.yy',
                'y' => 'MM.yy - MM.yy'),
            'yMEd' => array(
                'd' => 'E, dd.MM.yy - E, dd.MM.yy',
                'M' => 'E, dd.MM.yy - E, dd.MM.yy',
                'y' => 'E, dd.MM.yy - E, dd.MM.yy'),
            'yMMM' => array(
                'M' => 'MMM-MMM y',
                'y' => 'MMM y - MMM y'),
            'yMMMEd' => array(
                'd' => 'E, dd. - E, dd. MMM y',
                'M' => 'E, dd. MMM - E, dd. MMM y',
                'y' => 'E, dd. MMM y - E, dd. MMM y'),
            'yMMMM' => array(
                'M' => 'MMMM-MMMM y',
                'y' => 'MMMM y - MMMM y'),
            'yMMMd' => array(
                'd' => 'dd.-dd. MMM y',
                'M' => 'dd. MMM - dd. MMM y',
                'y' => 'dd. MMM y - dd. MMM y'),
            'yMd' => array(
                'd' => 'dd.MM.yy - dd.MM.yy',
                'M' => 'dd.MM.yy - dd.MM.yy',
                'y' => 'dd.MM.yy - dd.MM.yy')
        );
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'dateinterval', array('gregorian', 'yMMMM', 'y'));
        $this->assertEquals("MMMM y - MMMM y", $value);
    }

    /**
     * test for reading intervalformat from locale
     * expected array
     */
    public function testUnit()
    {
        $value = Cldr::getList('de_AT', 'unit');
        $result = array(
            'day' => array('one' => '{0} Tag', 'other' => '{0} Tage'),
            'hour' => array('one' => '{0} Stunde', 'other' => '{0} Stunden'),
            'minute' => array('one' => '{0} Minute', 'other' => '{0} Minuten'),
            'month' => array('one' => '{0} Monat', 'other' => '{0} Monate'),
            'second' => array('one' => '{0} Sekunde', 'other' => '{0} Sekunden'),
            'week' => array('one' => '{0} Woche', 'other' => '{0} Wochen'),
            'year' => array('one' => '{0} Jahr', 'other' => '{0} Jahre')
        );
        $this->assertEquals($result, $value);

        $value = Cldr::getContent('de_AT', 'unit', array('day', 'one'));
        $this->assertEquals('{0} Tag', $value);
    }
}
