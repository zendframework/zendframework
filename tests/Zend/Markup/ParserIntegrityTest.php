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
 * @package    Zend_Json
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';

if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Markup_ParserIntegrityTest::main");
}

require_once 'Zend/Markup.php';

/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage UnitTests
 * @group      Zend_Markup
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Markup_ParserIntegrityTest extends PHPUnit_Framework_TestCase
{

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Markup_MarkupTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testBbcodeParser()
    {
        $parser = Zend_Markup::factory('bbcode')->getParser();

        $value  = '[b][s][i]foobar[/i][/s][/b]';
        $output = '';

        $tree = $parser->parse($value);

        // iterate trough the tree and check if we can generate the original value
        $iterator = new RecursiveIteratorIterator($tree, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $token) {
            $output .= $token->getTag();

            if ($token->getStopper() != '') {
                $token->addChild(new Zend_Markup_Token(
                    $token->getStopper(),
                    Zend_Markup_Token::TYPE_NONE,
                    '', array(), $token)
                );
            }
        }

        $this->assertEquals($value, $output);
    }

}

// Call Zend_Markup_BbcodeTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Markup_ParserIntegrityTest::main") {
    Zend_Markup_BbcodeTest::main();
}
