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
 * @package    Zend_Captcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Captcha_DumbTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Captcha_DumbTest::main");
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Zend/Form/Element/Captcha.php';
require_once 'Zend/View.php';

/**
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Captcha_DumbTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Captcha_DumbTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        if (isset($this->word)) {
            unset($this->word);
        }

        $this->element = new Zend_Form_Element_Captcha(
            'captchaD', 
            array(
                'captcha' => array(
                    'Dumb', 
                    'sessionClass' => 'Zend_Captcha_DumbTest_SessionContainer'
                )
            )
        );
        $this->captcha =  $this->element->getCaptcha();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testRendersWordInReverse()
    {
        $id   = $this->captcha->generate('test');
        $word = $this->captcha->getWord();
        $html = $this->captcha->render(new Zend_View);
        $this->assertContains(strrev($word), $html);
        $this->assertNotContains($word, $html);
    }
}

class Zend_Captcha_DumbTest_SessionContainer
{
    protected static $_word;

    public function __get($name)
    {
        if ('word' == $name) {
            return self::$_word;
        }

        return null;
    }

    public function __set($name, $value)
    {
        if ('word' == $name) {
            self::$_word = $value;
        } else {
            $this->$name = $value;
        }
    }

    public function __isset($name)
    {
        if (('word' == $name) && (null !== self::$_word))  {
            return true;
        }

        return false;
    }

    public function __call($method, $args)
    {
        switch ($method) {
            case 'setExpirationHops':
            case 'setExpirationSeconds':
                $this->$method = array_shift($args);
                break;
            default:
        }
    }
}

// Call Zend_Captcha_DumbTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Captcha_DumbTest::main") {
    Zend_Captcha_DumbTest::main();
}
