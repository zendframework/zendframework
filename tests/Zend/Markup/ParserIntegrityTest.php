<?php
// Call Zend_Json_ServerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Markup_ParserIntegrityTest::main");
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Zend/Markup.php';

/**
 * Test class for Zend_Markup
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
