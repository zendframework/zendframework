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

zf_main();

/**
 * zf_main() - The main() function to run
 */
function zf_main() {
    global $_zf;
    $_zf = array();
    zf_setup_home_directory();
    zf_setup_storage_directory();
    zf_setup_config_file();
    zf_setup_php_runtime();
    zf_setup_tool_runtime();
    zf_run($_zf);
}

function zf_setup_home_directory() {
    global $_zf;
    
    // check for explicity set ENV var ZF_HOME
    if (($zfHome = getenv('ZF_HOME')) && file_exists($zfHome)) {
        $_zf['HOME'] = $zfHome;
    } elseif (($home = getenv('HOME'))) {
        $_zf['HOME'] = $home;
    } elseif (($home = getenv('HOMEPATH'))) {
        $_zf['HOME'] = $home;
    }
    
    $homeRealpath = realpath($_zf['HOME']);
    
    if ($homeRealpath) {
        $_zf['HOME'] = $homeRealpath;
    } else {
        unset($_zf['HOME']);
    }
    
}

function zf_setup_storage_directory() {
    global $_zf;
    
    if (($zfStorage = getenv('ZF_STORAGE_DIR')) && file_exists($zfStorage)) {
        $_zf['STORAGE_DIR'] = $zfStorage;
    } elseif (isset($_zf['HOME']) && file_exists($_zf['HOME'] . '/.zf/')) {
        $_zf['STORAGE_DIR'] = $_zf['HOME'] . '/.zf/';
    } else {
        return;
    }
    
    $storageRealpath = realpath($_zf['STORAGE_DIR']);
    
    if ($storageRealpath) {
        $_zf['STORAGE_DIR'] = $storageRealpath;
    } else {
        unset($_zf['STORAGE_DIR']);
    }
    
}

function zf_setup_config_file() {
    global $_zf;
    
    if (($zfConfigFile = getenv('ZF_CONFIG_FILE')) && file_exists($zfConfigFile)) {
        $_zf['CONFIG_FILE'] = $zfConfigFile;
    } elseif (isset($_zf['HOME'])) {
        if (file_exists($_zf['HOME'] . '/.zf.ini')) {
            $_zf['CONFIG_FILE'] = $_zf['HOME'] . '/.zf.ini';    
        } elseif (file_exists($_zf['HOME'] . '/zf.ini')) {
            $_zf['CONFIG_FILE'] = $_zf['HOME'] . '/zf.ini';
        }
    }

    if (isset($_zf['CONFIG_FILE']) && ($zrealpath = realpath($_zf['CONFIG_FILE']))) {

        $_zf['CONFIG_FILE'] = $zrealpath;
        
        $zsuffix = substr($_zf['CONFIG_FILE'], -4);
        
        if ($zsuffix === '.ini') {
            $_zf['CONFIG_TYPE'] = 'ini';
        } else {
            unset($_zf['CONFIG_FILE']);
        }

    }
    
}



function zf_setup_php_runtime() {
    global $_zf;
    if (!isset($_zf['CONFIG_TYPE']) || $_zf['CONFIG_TYPE'] !== 'ini') {
        return;
    }
    $zfini_settings = parse_ini_file($_zf['CONFIG_FILE']);
    $phpini_settings = ini_get_all();
    foreach ($zfini_settings as $zfini_key => $zfini_value) {
        if (substr($zfini_key, 0, 4) === 'php.') {
            $phpini_key = substr($zfini_key, 4); 
            if (array_key_exists($phpini_key, $phpini_settings)) {
                ini_set($phpini_key, $zfini_value);
            }
        }
    }
}

function zf_setup_tool_runtime() {
    //global $_zf;

    // last ditch efforts
    if (zf_try_client_load()) {
        return;
    }
    
    $zfIncludePath['original'] = get_include_path();
    
    // if ZF is not in the include_path, but relative to this file, put it in the include_path
    if (($zfIncludePath['prepend'] = getenv('ZEND_TOOL_INCLUDE_PATH_PREPEND')) || ($zfIncludePath['whole'] = getenv('ZEND_TOOL_INCLUDE_PATH'))) {
        if (isset($zfIncludePath['prepend']) && ($zfIncludePath['prepend'] !== false) && (($zfIncludePath['prependRealpath'] = realpath($zfIncludePath['prepend'])) !== false)) {
            set_include_path($zfIncludePath['prependRealpath'] . PATH_SEPARATOR . $zfIncludePath['original']);
        } elseif (isset($zfIncludePath['whole']) && ($zfIncludePath['whole'] !== false) && (($zfIncludePath['wholeRealpath'] = realpath($zfIncludePath['whole'])) !== false)) {
            set_include_path($zfIncludePath['wholeRealpath']);
        }
    }
    
    if (zf_try_client_load()) {
        return;
    }
    
    $zfIncludePath['relativePath'] = dirname(__FILE__) . '/../library/';
    if (file_exists($zfIncludePath['relativePath'] . 'Zend/Tool/Framework/Client/Console.php')) {
        set_include_path(realpath($zfIncludePath['relativePath']) . PATH_SEPARATOR . get_include_path());
    }

    if (!zf_try_client_load()) {
        zf_display_error();
        exit(1);
    }
    
}

function zf_try_client_load() {
    $loaded = @include_once 'Zend/Tool/Framework/Client/Console.php';
    return $loaded;
}

/**
 * zf_display_error()
 */
function zf_display_error() {
    
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

    echo '    attempted include_path: ' . get_include_path() . PHP_EOL;
    echo '    script location: ' . $_SERVER['SCRIPT_NAME'] . PHP_EOL;

}

function zf_run($zfConfig) {
    global $_zf;
    unset($_zf);
    
    $configOptions = array();
    if (isset($zfConfig['CONFIG_FILE'])) {
        $configOptions['configFilepath'] = $zfConfig['CONFIG_FILE'];
    }
    if (isset($zfConfig['STORAGE_DIR'])) {
        $configOptions['storageDirectory'] = $zfConfig['STORAGE_DIR']; 
    }

    Zend_Tool_Framework_Client_Console::main($configOptions);    
}



