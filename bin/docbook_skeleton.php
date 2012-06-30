#!/usr/bin/env php
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
 * @package    Zend_DocBook
 * @subpackage Exception
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

use Zend\Console;
use Zend\DocBook\ClassParser;
use Zend\DocBook\SkeletonGenerator;
use Zend\Code\Reflection\ClassReflection as ReflectionClass;
use Zend\Loader\StandardAutoloader;

/**
 * Generate Docbook XML skeleton for a given class as a documentation stub.
 *
 * Additionally, it parses all public methods of the class and creates stub 
 * documentation for each in the "Available Methods" section of the class
 * documentation.
 *
 * Usage:
 * --help|-h                Get usage message
 * --class|-c [ <string> ]  Class name for which to provide documentation
 * --output|-o [ <string> ] Where to write generated documentation. By default,
 *                          assumes documentation/manual/en/module_specs, in a 
 *                          file named after the provided class (in the form of
 *                          "zend.component.class-name.xml")
 */

$libPath = getenv('LIB_PATH') ? getenv('LIB_PATH') : __DIR__ . '/../library';
if (!is_dir($libPath)) {
    // Try to load StandardAutoloader from include_path
    if (false === include('Zend/Loader/StandardAutoloader.php')) {
        echo "Unable to locate autoloader via include_path; aborting" . PHP_EOL;
        exit(2);
    }
} else {
    // Try to load StandardAutoloader from library
    if (false === include($libPath . '/Zend/Loader/StandardAutoloader.php')) {
        echo "Unable to locate autoloader via library; aborting" . PHP_EOL;
        exit(2);
    }
}

// Setup autoloading
$loader = new StandardAutoloader(array('autoregister_zf' => true));
$loader->register();

$rules = array(
    'help|h'        => 'Get usage message',
    'class|c=s'     => 'Class for which to create a docbook skeleton',
    'output|o-s'    => 'Where to write skeleton file; if not provided, assumes documentation/manual/en/module_specs/<class id>.xml"',
);

$docbookPath = __DIR__ . '/../documentation/manual/en/module_specs';
$docbookFile = false;

// Parse options
try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Console\Exception\RuntimeException $e) {
    // Error creating or parsing options; show usage message
    echo $e->getUsageMessage();
    exit(2);
}

// Help requested
if ($opts->getOption('h')) {
    echo $opts->getUsageMessage();
    exit();
}

// Were we provided a class name?
if (!isset($opts->c)) {
    // No class name == no execution
    echo "Must provide a class via the -c or --class option\n\n";
    echo $opts->getUsageMessage();
    exit(2);
}
// Valid class name?
$class = $opts->c;
if (!class_exists($class) && !interface_exists($class)) {
    // Invalid class name == no execution
    printf("Invalid class '%s' provided' class not found\n\n", $class);
    echo $opts->getUsageMessage();
    exit(2);
}

// Was a specific filename provided for the generated output?
if (isset($opts->o)) {
    $docbookPath = dirname($opts->o);
    $docbookFile = basename($opts->o);
}

$parser    = new ClassParser(new ReflectionClass($class));
$generator = new SkeletonGenerator($parser);
$xml       = $generator->generate();

// Normalize per CS
$xml = strtr($xml, array(
    '  '              => '    ',              // 4 space tabs
    '</info>'         => "</info>\n",         // Extra newline between blocks
    '</section>'      => "</section>\n",
    '</term>'         => "</term>\n",
    '</varlistentry>' => "</varlistentry>\n",
));

// Strip extra whitespace at end of document
$xml = str_replace("</section>\n\n</section>\n", "</section>\n</section>", $xml);

// Write file
if (!$docbookFile) {
    // Auto-generate filename based on class ID
    $docbookFile = $parser->getId() . '.xml';
}
$path = $docbookPath . DIRECTORY_SEPARATOR . $docbookFile;
file_put_contents($path, $xml);

echo "[DONE] Wrote Docbook XML skeleton to $path\n";
