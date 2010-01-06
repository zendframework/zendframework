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
 * @package    Zend_Session
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/** Test helper */
// require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

// Do not include TestHelper.php since it takes too much time
// Directly include part of it
$zfRoot        = dirname(dirname(dirname(dirname(__FILE__))));
$zfCoreLibrary = "$zfRoot/library";
$zfCoreTests   = "$zfRoot/tests";

$path = array(
    $zfCoreLibrary,
    $zfCoreTests,
    get_include_path()
    );
set_include_path(implode(PATH_SEPARATOR, $path));


/**
 * @see Zend_Session
 */
require_once 'Zend/Session.php';


/**
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Session
 */
class Zend_Session_TestHelper
{
    /**
     * Runs the test method specified via command line arguments.
     *
     * @param  array $argv
     * @return integer
     */
    public function run(array $argv)
    {
        if (!isset($argv[0]) || !isset($argv[1])) {
            echo "Usage: {$argv[0]} <test name>\n";
            return 1;
        }

        $testMethod = 'do' . ucfirst($argv[1]);

        if (!method_exists($this, $testMethod)) {
            echo "Invalid test: '{$argv[1]}'\n";
            return 2;
        }

        array_shift($argv);
        array_shift($argv);

        return $this->$testMethod($argv);
    }

    /**
     * @param  array $args
     * @return integer Always returns zero.
     */
    public function doExpireAll(array $args)
    {
        Zend_Session::setOptions(array('remember_me_seconds' => 15, 'gc_probability' => 2));
        session_id($args[0]);
        if (isset($args[1]) && !empty($args[1])) {
            $s = new Zend_Session_Namespace($args[1]);
        }
        else {
            $s = new Zend_Session_Namespace();
        }
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        Zend_Session::expireSessionCookie();
        Zend_Session::writeClose();
        echo $result;

        return 0;
    }

    /**
     * @param  array $args
     * @return integer Always returns zero.
     */
    public function doSetArray(array $args)
    {
        $GLOBALS['fpc'] = 'set';
        session_id($args[0]);
        $s = new Zend_Session_Namespace($args[1]);
        array_shift($args);
        $s->astring = 'happy';

        // works, even for broken versions of PHP
        // $s->someArray = array( & $args ) ;
        // $args['OOOOOOOOOOOOOOOO'] = 'YYYYYYYYYYYYYYYYYYYYYYYYYYYYY';

        $s->someArray = $args;
        $s->someArray['bee'] = 'honey'; // Repeating this line twice "solves" the problem for some versions of PHP,
        $s->someArray['bee'] = 'honey'; // but PHP 5.2.1 has the real fix for ZF-800.
        $s->someArray['ant'] = 'sugar';
        $s->someArray['dog'] = 'cat';
        // file_put_contents('out.sessiontest.set', (str_replace(array("\n", ' '),array(';',''), print_r($_SESSION, true))) );
        $s->serializedArray = serialize($args);

        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === ". (print_r($val,true)) .';';
        }

        Zend_Session::writeClose();

        return 0;
    }

    /**
     * @param  array $args
     * @return integer Always returns zero.
     */
    public function doGetArray(array $args)
    {
        $GLOBALS['fpc'] = 'get';
        session_id($args[0]);
        if (isset($args[1]) && !empty($args[1])) {
            $s = new Zend_Session_Namespace($args[1]);
        }
        else {
            $s = new Zend_Session_Namespace();
        }
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === ". (str_replace(array("\n", ' '),array(';',''), print_r($val, true))) .';';
        }
        // file_put_contents('out.sesstiontest.get', print_r($s->someArray, true));
        Zend_Session::writeClose();
        echo $result;

        return 0;
    }
}


$testHelper = new Zend_Session_TestHelper();

exit($testHelper->run($argv));

