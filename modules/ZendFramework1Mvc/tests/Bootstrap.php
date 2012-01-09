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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/*
 * Set error reporting to the level to which Zend Framework code must comply.
 */
error_reporting( E_ALL | E_STRICT );

/*
 * Determine the root, library, and tests directories of the framework
 * distribution.
 */
$zfModLibrary  = realpath(__DIR__ . '/../library'); 
$zfModTests    = realpath(__DIR__);
$zfCoreLibrary = realpath(__DIR__ . '/../../../library');

/*
 * Prepend the Zend Framework library/ and tests/ directories to the
 * include_path. This allows the tests to run out of the box and helps prevent
 * loading other copies of the framework code and tests that would supersede
 * this copy.
 */
$path = array(
    '.',
    $zfModLibrary,
    $zfModTests,
    $zfCoreLibrary,
    get_include_path(),
);
set_include_path(implode(PATH_SEPARATOR, $path));

/**
 * Setup autoloading
 */
include __DIR__ . '/_autoload.php';

/*
 * Load the user-defined test configuration file, if it exists; otherwise, load
 * the default configuration.
 */
if (is_readable($zfModTests . '/TestConfiguration.php')) {
    require_once $zfModTests . '/TestConfiguration.php';
} else {
    require_once $zfModTests . '/TestConfiguration.php.dist';
}

if (defined('TESTS_GENERATE_REPORT') 
    && TESTS_GENERATE_REPORT === true 
    && version_compare(PHPUnit_Runner_Version::id(), '3.1.6', '>=')
) {
    $codeCoverageFilter = PHP_CodeCoverage_Filter::getInstance();
        
    /*
     * Omit from code coverage reports the contents of the tests directory
     */
    foreach (array('.php', '.phtml', '.csv', '.inc') as $suffix) {
        $codeCoverageFilter->addDirectoryToBlacklist($zfCoreTests, $suffix);
    }

    $codeCoverageFilter->addDirectoryToBlacklist(PEAR_INSTALL_DIR);
    $codeCoverageFilter->addDirectoryToBlacklist(PHP_LIBDIR);

    unset($codeCoverageFilter);
}


/**
 * Start output buffering, if enabled
 */
if (defined('TESTS_ZEND_OB_ENABLED') && constant('TESTS_ZEND_OB_ENABLED')) {
    ob_start();
}

/*
 * Unset global variables that are no longer needed.
 */
unset($zfCoreLibrary, $zfModLibrary, $zfModTests, $path);
