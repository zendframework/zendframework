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
 * @package    Zend_Exception
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @category   Zend
 * @package    Zend_resources
 * @subpackage UnitTests
 * @group      Zend_Exception
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class resources_languages_Zend_ValidateTest extends PHPUnit_Framework_TestCase
{

    protected $_langDir      = null;
    protected $_languages    = array();
    protected $_translations = array();

    public function setUp()
    {
        $this->_langDir = dirname(dirname(dirname(dirname(__FILE__))))
                        . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'languages';
        if (!is_readable($this->_langDir)) {
            throw new Exception('Language resource directory "'.$this->_langDir.'" not readable.');
        }

        // detect languages
        foreach (new DirectoryIterator($this->_langDir) as $entry) {
            if (!$entry->isDir()) {
                continue;
            }

            // skip "." or ".." or ".svn"
            $fname = $entry->getFilename();
            if ($fname[0] == '.') {
                continue;
            }

            // add all languages for testIsLocale
            $this->_languages[] = $fname;

            // include Zend_Validate translation tables
            $translationFile = $entry->getPathname() . DIRECTORY_SEPARATOR . 'Zend_Validate.php';
            if (file_exists($translationFile)) {
                $translation = include $translationFile;
                if (!is_array($translation)) {
                    $this->fail("Invalid or empty translation table found for language '{$fname}'");
                }
                $this->_translations[$fname] = $translation;
            }
        }
    }

    public function testIsLocale()
    {
        foreach ($this->_languages as $lang) {
            if (!Zend_Locale::isLocale($lang, true, false)) {
                $this->fail("Language directory '{$lang}' not a valid locale");
            }
        }
    }

    public function testEnglishKeySameAsValue()
    {
        foreach ($this->_translations['en'] as $k => $v) {
            $this->assertEquals($k, $v);
        }
    }

    public function testTranslationAvailableInEnglish()
    {
        foreach ($this->_translations as $lang => $translation) {
            if ($lang == 'en') {
                continue;
            }

            foreach ($translation as $k => $v) {
                $this->assertTrue(
                    isset($this->_translations['en'][$k]),
                    $lang . ': The key "' . $k . '" isn\'t available within english translation file'
                );
            }
        }
    }

    public function testTranslationDiffersFromEnglish()
    {
        foreach ($this->_translations as $lang => $translation) {
            if ($lang == 'en') {
                continue;
            }

            foreach ($translation as $k => $v) {
                $this->assertTrue( ($k != $v),
                    $lang . ': The translated message "' . $v . '" is the same the english version'
                );
            }
        }
    }

    public function testPlaceholder()
    {
        foreach ($this->_translations as $lang => $translation) {
            if ($lang == 'en') { // not needed to test - see testEnglishKeySameAsValue
                continue;
            }

            foreach ($translation as $k => $v) {
                if (preg_match_all('/(\%.+\%)/U', $k, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $this->assertContains($match[1], $v,
                            $lang . ': Missing placeholder "' . $match[1] . '" within "' . $v . '"');
                    }
                }
            }
        }
    }

    public function testAllTranslated()
    {
        foreach ($this->_translations['en'] as $enK => $enV) {
            foreach ($this->_translations as $lang => $translation) {
                if ($lang == 'en') {
                    continue;
                }

                $this->assertTrue(isset($translation[$enK]),
                    $lang . ': Message "' . $enK . '" not translated');
            }
        }
    }

}
