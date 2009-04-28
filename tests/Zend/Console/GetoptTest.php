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
 * @package    Zend
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * Zend_Console_Getopt
 */
require_once 'Zend/Console/Getopt.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Console_Getopt
 * @subpackage UnitTests
 */
class Zend_Console_GetoptTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if(ini_get('register_argc_argv') == false) {
            $this->markTestSkipped("Cannot Test Zend_Console_Getopt without 'register_argc_argv' ini option true.");
        }
    }

    public function testGetoptShortOptionsGnuMode()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '-p', 'p_arg'));
        $this->assertEquals(true, $opts->a);
        $this->assertNull(@$opts->b);
        $this->assertEquals($opts->p, 'p_arg');
    }

    public function testGetoptLongOptionsZendMode()
    {
        $opts = new Zend_Console_Getopt(array(
                'apple|a' => 'Apple option',
                'banana|b' => 'Banana option',
                'pear|p=s' => 'Pear option'
            ),
            array('-a', '-p', 'p_arg'));
        $this->assertTrue($opts->apple);
        $this->assertNull(@$opts->banana);
        $this->assertEquals($opts->pear, 'p_arg');
    }

    public function testGetoptZendModeEqualsParam()
    {
        $opts = new Zend_Console_Getopt(array(
                'apple|a' => 'Apple option',
                'banana|b' => 'Banana option',
                'pear|p=s' => 'Pear option'
            ),
            array('--pear=pear.phpunit.de'));
        $this->assertEquals($opts->pear, 'pear.phpunit.de');
    }

    public function testGetoptToString()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '-p', 'p_arg'));
        $this->assertEquals($opts->__toString(), 'a=true p=p_arg');
    }

    public function testGetoptDumpString()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '-p', 'p_arg'));
        $this->assertEquals($opts->toString(), 'a=true p=p_arg');
    }

    public function testGetoptDumpArray()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '-p', 'p_arg'));
        $this->assertEquals(implode(',', $opts->toArray()), 'a,p,p_arg');
    }

    public function testGetoptDumpJson()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '-p', 'p_arg'));
        $this->assertEquals($opts->toJson(),
            '{"options":[{"option":{"flag":"a","parameter":true}},{"option":{"flag":"p","parameter":"p_arg"}}]}');

    }

    public function testGetoptDumpXml()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '-p', 'p_arg'));
        $this->assertEquals($opts->toXml(),
            "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<options><option flag=\"a\"/><option flag=\"p\" parameter=\"p_arg\"/></options>\n");
    }

    public function testGetoptExceptionForMissingFlag()
    {
        try {
            $opts = new Zend_Console_Getopt(array('|a'=>'Apple option'));
            $this->fail('Expected to catch Zend_Console_Getopt_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Console_Getopt_Exception', $e,
                'Expected Zend_Console_Getopt_Exception, got '.get_class($e));
            $this->assertEquals($e->getMessage(),
                'Blank flag not allowed in rule "|a".');
        }
    }

    public function testGetoptExceptionForDuplicateFlag()
    {
        try {
            $opts = new Zend_Console_Getopt(
                array('apple|apple'=>'apple-option'));
            $this->fail('Expected to catch Zend_Console_Getopt_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Console_Getopt_Exception', $e,
                'Expected Zend_Console_Getopt_Exception, got '.get_class($e));
            $this->assertEquals($e->getMessage(),
                'Option "--apple" is being defined more than once.');
        }

        try {
            $opts = new Zend_Console_Getopt(
                array('a'=>'Apple option', 'apple|a'=>'Apple option'));
            $this->fail('Expected to catch Zend_Console_Getopt_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Console_Getopt_Exception', $e,
                'Expected Zend_Console_Getopt_Exception, got '.get_class($e));
            $this->assertEquals($e->getMessage(),
                'Option "-a" is being defined more than once.');
        }
    }

    public function testGetoptAddRules()
    {
        $opts = new Zend_Console_Getopt(
            array(
                'apple|a' => 'Apple option',
                'banana|b' => 'Banana option'
            ),
            array('--pear', 'pear_param'));
        try {
            $opts->parse();
            $this->fail('Expected to catch Zend_Console_Getopt_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Console_Getopt_Exception', $e,
                'Expected Zend_Console_Getopt_Exception, got '.get_class($e));
            $this->assertEquals($e->getMessage(), 'Option "pear" is not recognized.');
        }
        $opts->addRules(array('pear|p=s' => 'Pear option'));
        $this->assertEquals($opts->pear, 'pear_param');
    }

    public function testGetoptExceptionMissingParameter()
    {
        $opts = new Zend_Console_Getopt(
            array(
                'apple|a=s' => 'Apple with required parameter',
                'banana|b' => 'Banana'
            ),
            array('--apple'));
        try {
            $opts->parse();
            $this->fail('Expected to catch Zend_Console_Getopt_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Console_Getopt_Exception', $e,
                'Expected Zend_Console_Getopt_Exception, got '.get_class($e));
            $this->assertEquals($e->getMessage(), 'Option "apple" requires a parameter.');
        }
    }

    public function testGetoptOptionalParameter()
    {
        $opts = new Zend_Console_Getopt(
            array(
                'apple|a-s' => 'Apple with optional parameter',
                'banana|b' => 'Banana'
            ),
            array('--apple', '--banana'));
        $this->assertTrue($opts->apple);
        $this->assertTrue($opts->banana);
    }

    public function testGetoptIgnoreCaseGnuMode()
    {
        $opts = new Zend_Console_Getopt('aB', array('-A', '-b'),
            array(Zend_Console_Getopt::CONFIG_IGNORECASE => true));
        $this->assertEquals(true, $opts->a);
        $this->assertEquals(true, $opts->B);
    }

    public function testGetoptIgnoreCaseZendMode()
    {
        $opts = new Zend_Console_Getopt(
            array(
                'apple|a' => 'Apple-option',
                'Banana|B' => 'Banana-option'
            ),
            array('--Apple', '--bAnaNa'),
            array(Zend_Console_Getopt::CONFIG_IGNORECASE => true));
        $this->assertEquals(true, $opts->apple);
        $this->assertEquals(true, $opts->BANANA);
    }

    public function testGetoptIsSet()
    {
        $opts = new Zend_Console_Getopt('ab', array('-a'));
        $this->assertTrue(isset($opts->a));
        $this->assertFalse(isset($opts->b));
    }

    public function testGetoptIsSetAlias()
    {
        $opts = new Zend_Console_Getopt('ab', array('-a'));
        $opts->setAliases(array('a' => 'apple', 'b' => 'banana'));
        $this->assertTrue(isset($opts->apple));
        $this->assertFalse(isset($opts->banana));
    }

    public function testGetoptIsSetInvalid()
    {
        $opts = new Zend_Console_Getopt('ab', array('-a'));
        $opts->setAliases(array('a' => 'apple', 'b' => 'banana'));
        $this->assertFalse(isset($opts->cumquat));
    }

    public function testGetoptSet()
    {
        $opts = new Zend_Console_Getopt('ab', array('-a'));
        $this->assertFalse(isset($opts->b));
        $opts->b = true;
        $this->assertTrue(isset($opts->b));
    }

    public function testGetoptSetBeforeParse()
    {
        $opts = new Zend_Console_Getopt('ab', array('-a'));
        $opts->b = true;
        $this->assertTrue(isset($opts->b));
    }

    public function testGetoptUnSet()
    {
        $opts = new Zend_Console_Getopt('ab', array('-a'));
        $this->assertTrue(isset($opts->a));
        unset($opts->a);
        $this->assertFalse(isset($opts->a));
    }

    public function testGetoptUnSetBeforeParse()
    {
        $opts = new Zend_Console_Getopt('ab', array('-a'));
        unset($opts->a);
        $this->assertFalse(isset($opts->a));
    }

    public function testGetoptAddArguments()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a'));
        $this->assertNull(@$opts->p);
        $opts->addArguments(array('-p', 'p_arg'));
        $this->assertEquals($opts->p, 'p_arg');
    }

    public function testGetoptRemainingArgs()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '--', 'file1', 'file2'));
        $this->assertEquals(implode(',', $opts->getRemainingArgs()), 'file1,file2');
        $opts = new Zend_Console_Getopt('abp:', array('-a', 'file1', 'file2'));
        $this->assertEquals(implode(',', $opts->getRemainingArgs()), 'file1,file2');
    }

    public function testGetoptDashDashFalse()
    {
        try {
            $opts = new Zend_Console_Getopt('abp:', array('-a', '--', '--fakeflag'),
                array(Zend_Console_Getopt::CONFIG_DASHDASH => false));
            $opts->parse();
            $this->fail('Expected to catch Zend_Console_Getopt_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Console_Getopt_Exception', $e,
                'Expected Zend_Console_Getopt_Exception, got '.get_class($e));
            $this->assertEquals($e->getMessage(), 'Option "fakeflag" is not recognized.');
        }
    }

    public function testGetoptGetOptions()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a', '-p', 'p_arg'));
        $this->assertEquals(implode(',', $opts->getOptions()), 'a,p');
    }

    public function testGetoptGetUsageMessage()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-x'));
        $message = preg_replace('/Usage: .* \[ options \]/',
            'Usage: <progname> [ options ]',
            $opts->getUsageMessage());
        $message = preg_replace('/ /', '_', $message);
        $this->assertEquals($message,
            "Usage:_<progname>_[_options_]\n-a___________________\n-b___________________\n-p_<string>__________\n");
    }

    public function testGetoptUsageMessageFromException()
    {
        try {
            $opts = new Zend_Console_Getopt(array(
                'apple|a-s' => 'apple',
                'banana1|banana2|banana3|banana4' => 'banana',
                'pear=s' => 'pear'),
                array('-x'));
            $opts->parse();
            $this->fail('Expected to catch Zend_Console_Getopt_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Console_Getopt_Exception', $e,
                'Expected Zend_Console_Getopt_Exception, got '.get_class($e));
            $message = preg_replace('/Usage: .* \[ options \]/',
                'Usage: <progname> [ options ]',
                $e->getUsageMessage());
            $message = preg_replace('/ /', '_', $message);
            $this->assertEquals($message,
                "Usage:_<progname>_[_options_]\n--apple|-a_[_<string>_]_________________apple\n--banana1|--banana2|--banana3|--banana4_banana\n--pear_<string>_________________________pear\n");

        }
    }

    public function testGetoptSetAliases()
    {
        $opts = new Zend_Console_Getopt('abp:', array('--apple'));
        $opts->setAliases(array('a' => 'apple'));
        $this->assertTrue($opts->a);
    }

    public function testGetoptSetAliasesIgnoreCase()
    {
        $opts = new Zend_Console_Getopt('abp:', array('--apple'), 
            array(Zend_Console_Getopt::CONFIG_IGNORECASE => true));
        $opts->setAliases(array('a' => 'APPLE'));
        $this->assertTrue($opts->apple);
    }

    public function testGetoptSetAliasesWithNamingConflict()
    {
        $opts = new Zend_Console_Getopt('abp:', array('--apple'));
        $opts->setAliases(array('a' => 'apple'));
        try {
            $opts->setAliases(array('b' => 'apple'));
            $this->fail('Expected to catch Zend_Console_Getopt_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Console_Getopt_Exception', $e,
                'Expected Zend_Console_Getopt_Exception, got '.get_class($e));
            $this->assertEquals($e->getMessage(), 'Option "--apple" is being defined more than once.');
        }
    }

    public function testGetoptSetAliasesInvalid()
    {
        $opts = new Zend_Console_Getopt('abp:', array('--apple'));
        $opts->setAliases(array('c' => 'cumquat'));
        $opts->setArguments(array('-c'));
        try {
            $opts->parse();
            $this->fail('Expected to catch Zend_Console_Getopt_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Console_Getopt_Exception', $e,
                'Expected Zend_Console_Getopt_Exception, got '.get_class($e));
            $this->assertEquals('Option "c" is not recognized.', $e->getMessage());
        }
    }

    public function testGetoptSetHelp()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a'));
        $opts->setHelp(array(
            'a' => 'apple',
            'b' => 'banana',
            'p' => 'pear'));
        $message = preg_replace('/Usage: .* \[ options \]/',
            'Usage: <progname> [ options ]',
            $opts->getUsageMessage());
        $message = preg_replace('/ /', '_', $message);
        $this->assertEquals($message, 
            "Usage:_<progname>_[_options_]\n-a___________________apple\n-b___________________banana\n-p_<string>__________pear\n");

    }

    public function testGetoptSetHelpInvalid()
    {
        $opts = new Zend_Console_Getopt('abp:', array('-a'));
        $opts->setHelp(array(
            'a' => 'apple',
            'b' => 'banana',
            'p' => 'pear',
            'c' => 'cumquat'));
        $message = preg_replace('/Usage: .* \[ options \]/',
            'Usage: <progname> [ options ]',
            $opts->getUsageMessage());
        $message = preg_replace('/ /', '_', $message);
        $this->assertEquals($message, 
            "Usage:_<progname>_[_options_]\n-a___________________apple\n-b___________________banana\n-p_<string>__________pear\n");
    }

    public function testGetoptCheckParameterType()
    {
        $opts = new Zend_Console_Getopt(array(
            'apple|a=i' => 'apple with integer',
            'banana|b=w' => 'banana with word',
            'pear|p=s' => 'pear with string',
            'orange|o-i' => 'orange with optional integer',
            'lemon|l-w' => 'lemon with optional word',
            'kumquat|k-s' => 'kumquat with optional string'));

        $opts->setArguments(array('-a', 327));
        $opts->parse();
        $this->assertEquals(327, $opts->a);

        $opts->setArguments(array('-a', 'noninteger'));
        try {
            $opts->parse();
            $this->fail('Expected to catch Zend_Console_Getopt_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Console_Getopt_Exception', $e,
                'Expected Zend_Console_Getopt_Exception, got '.get_class($e));
            $this->assertEquals($e->getMessage(), 'Option "apple" requires an integer parameter, but was given "noninteger".');
        }

        $opts->setArguments(array('-b', 'word'));
        $this->assertEquals('word', $opts->b);

        $opts->setArguments(array('-b', 'two words'));
        try {
            $opts->parse();
            $this->fail('Expected to catch Zend_Console_Getopt_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Console_Getopt_Exception', $e,
                'Expected Zend_Console_Getopt_Exception, got '.get_class($e));
            $this->assertEquals($e->getMessage(), 'Option "banana" requires a single-word parameter, but was given "two words".');
        }

        $opts->setArguments(array('-p', 'string'));
        $this->assertEquals('string', $opts->p);

        $opts->setArguments(array('-o', 327));
        $this->assertEquals(327, $opts->o);

        $opts->setArguments(array('-o'));
        $this->assertTrue($opts->o);

        $opts->setArguments(array('-l', 'word'));
        $this->assertEquals('word', $opts->l);

        $opts->setArguments(array('-k', 'string'));
        $this->assertEquals('string', $opts->k);

    }

    /**
     * @group ZF-2295
     */
    public function testRegisterArgcArgvOffThrowsException()
    {
        $argv = $_SERVER['argv'];
        unset($_SERVER['argv']);

        try {
            $opts = new Zend_Console_GetOpt('abp:');
            $this->fail();
        } catch(Zend_Console_GetOpt_Exception $e) {
            $this->assertContains('$_SERVER["argv"]', $e->getMessage());
        }

        $_SERVER['argv'] = $argv;
    }
    
    /**
     * Test to ensure that dashed long names will parse correctly
     * 
     * @group ZF-4763
     */
    public function testDashWithinLongOptionGetsParsed()
    {
        $opts = new Zend_Console_Getopt(
            array( // rules
                'man-bear|m-s' => 'ManBear with dash',
                'man-bear-pig|b=s' => 'ManBearPid with dash',
                ),
            array( // arguments
                '--man-bear-pig=mbp',
                '--man-bear',
                'foobar'
                )
            );
        
        $opts->parse();
        $this->assertEquals('foobar', $opts->getOption('man-bear'));
        $this->assertEquals('mbp', $opts->getOption('man-bear-pig'));
    }

    /**
     * @group ZF-2064
     */
    public function testAddRulesDoesNotThrowWarnings()
    {
        // Fails if warning is thrown: Should not happen!
        $opts = new Zend_Console_Getopt('abp:');
        $opts->addRules(
          array(
            'verbose|v' => 'Print verbose output'
          )
        );
    }

    /**
     * @group ZF-5345
     */
    public function testUsingDashWithoutOptionNameAsLastArgumentIsRecognizedAsRemainingArgument()
    {
        $opts = new Zend_Console_Getopt("abp:", array("-"));
        $opts->parse();

        $this->assertEquals(1, count($opts->getRemainingArgs()));
        $this->assertEquals(array("-"), $opts->getRemainingArgs());
    }

    /**
     * @group ZF-5345
     */
    public function testUsingDashWithoutOptionNotAsLastArgumentThrowsException()
    {
        $opts = new Zend_Console_Getopt("abp:", array("-", "file1"));
        try {
            $opts->parse();
            $this->fail();
        } catch(Exception $e) {
            $this->assertTrue($e instanceof Zend_Console_Getopt_Exception);
        }
    }
    
    /**
     * @group ZF-5624
     */
    public function testEqualsCharacterInLongOptionsValue()
    {
        $fooValue = 'some text containing an = sign which breaks';

        $opts = new Zend_Console_Getopt(
            array('foo=s' => 'Option One (string)'),
            array('--foo='.$fooValue)
        );
        $this->assertEquals($fooValue, $opts->foo);
    }
}
