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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

$_zf['original'] = get_include_path();

// if ZF is not in the include_path, but relative to this file, put it in the include_path
if (($_zf['prepend'] = getenv('ZEND_TOOL_INCLUDE_PATH_PREPEND')) || ($_zf['whole'] = getenv('ZEND_TOOL_INCLUDE_PATH'))) {
    if (isset($_zf['prepend']) && ($_zf['prepend'] !== false) && (($_zf['prependRealpath'] = realpath($_zf['prepend'])) !== false)) {
        set_include_path($_zf['prependRealpath'] . PATH_SEPARATOR . $_zf['original']);
    } elseif (isset($_zf['whole']) && ($_zf['whole'] !== false) && (($_zf['wholeRealpath'] = realpath($_zf['whole'])) !== false)) {
        set_include_path($_zf['wholeRealpath']);
    }
} 

// assume the include_path is good, and load the client/console
if ((@include_once 'Zend/Tool/Framework/Client/Console.php') === false) {
    // last chance, perhaps we can find zf relative to THIS file, if so, lets run
    $_zf['relativePath'] = dirname(__FILE__) . '/../library/';
    if (file_exists($_zf['relativePath'] . 'Zend/Tool/Framework/Client/Console.php')) {
        set_include_path(realpath($_zf['relativePath']) . PATH_SEPARATOR . get_include_path());
        include_once 'Zend/Tool/Framework/Client/Console.php';
    }
}

if (!class_exists('Zend_Tool_Framework_Client_Console')) {
    echo <<<EOS
    
***************************** ZF ERROR ********************************
In order to run the zf command, you need to ensure that Zend Framework
is inside your include_path.  If you are running this tool without 
ZendFramework in your include_path, you can alternatively set one of 
two environment variables to for this tool to work:

a) ZEND_TOOL_INCLUDE_PATH_PREPEND="/path/to/ZendFramework/library"

OR alternatively

b) ZEND_TOOL_INCLUDE_PATH="/path/to/ZendFramework/library"

The former (a) will make the specified Zend Framework first in the
include_path whereas the latter (b) will replace the include_path
with the specified path.

Information:

EOS;

    echo '    original include_path: ' . $_zf['original'] . PHP_EOL;
    echo '    attempted include_path: ' . get_include_path() . PHP_EOL;
    echo '    script location: ' . $_SERVER['SCRIPT_NAME'] . PHP_EOL;
    exit(1);    
}

// cleanup the global space
unset($_zf);

// run tool
Zend_Tool_Framework_Client_Console::main();
exit(0);
