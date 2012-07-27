#!/usr/bin/env php
<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendBin;

use Zend\Console;
use Zend\File\ClassFileLocator;
use Zend\Loader\StandardAutoloader;
use Zend\Text\Table;

/*
 * Convert a file from DocBook to reStructuredText format
 *
 * Note: this script has been created to convert the Zend Framework
 * documentation. We have not tested it with other docBook file formats.
 *
 * Usage:
 * --help|-h        Get usage message
 * --docbook|-d     Docbook file to convert
 * --output|-o      Output file in reStructuredText format; By default,
 *                  assumes <docbook>.rst
 */
define('INPUT_ENCODING', 'UTF-8');

iconv_set_encoding("internal_encoding", "UTF-8");
iconv_set_encoding("input_encoding", INPUT_ENCODING);
iconv_set_encoding("output_encoding", "UTF-8");

echo "DocBook to reStructuredText conversion for ZF documentation\n";
echo "-----------------------------------------------------------\n";

$libPath = getenv('LIB_PATH') ? getenv('LIB_PATH') : __DIR__ . '/../library';
if (!is_dir($libPath)) {
    // Try to load StandardAutoloader from include_path
    if (false === include('Zend/Loader/StandardAutoloader.php')) {
        echo "Unable to locate autoloader via include_path; aborting" . PHP_EOL;
        exit(2);
    }
} elseif (false === include($libPath . '/Zend/Loader/StandardAutoloader.php')) {
    echo "Unable to locate autoloader via library; aborting" . PHP_EOL;
    exit(2);
}

// Setup autoloading
$loader = new StandardAutoloader(array('autoregister_zf' => true));
$loader->register();

$rules = array(
    'help|h'      => 'Get usage message',
    'docbook|d-s' => 'Docbook file to convert',
    'output|o-s'  => 'Output file in reStructuredText format; By default assumes <docbook>.rst"',
);

try {
    $opts = new Console\Getopt($rules);
    $opts->parse();
} catch (Console\Exception\RuntimeException $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if (!$opts->getOptions() || $opts->getOption('h')) {
    echo $opts->getUsageMessage();
    exit(0);
}

$docbook = $opts->getOption('d');
if (!file_exists($docbook)) {
    echo "Error: the docbook file $docbook doesn't exist." . PHP_EOL;
    exit(2);
}

$rstFile = $opts->getOption('o');
if (empty($rstFile)) {
    $rstFile = $docbook;
    if ('.xml' === substr($rstFile, -4)) {
        $rstFile = substr($rstFile, 0, strlen($docbook) - 4);
    }
    $rstFile .= '.rst';
}

if (is_dir($rstFile)) {
    $rstFile = realpath($rstFile) . DIRECTORY_SEPARATOR;
    $rstFile .= RstConvert::xmlFileNameToRst(basename($docbook));
}

// Load the docbook file (input)
$xml = new \DOMDocument;
$xml->load($docbook);

$xsltFile = __DIR__ . '/doc2rst.xsl';

// Load the XSLT file
$xsl = new \DOMDocument;
if (!file_exists($xsltFile)) {
    echo "The $xsltFile is missing, I cannot proceed with the conversion." . PHP_EOL;
    exit(2);
}
$xsl->load($xsltFile);

$proc = new \XSLTProcessor;
$proc->registerPHPFunctions();
$proc->importStyleSheet($xsl);

echo "Writing to $rstFile\n";

$output = $proc->transformToXml($xml);
if (false === $output) {
    echo 'Error during the conversion' . PHP_EOL;
    exit(2);
}

// remove single spaces in the beginning of new lines
$lines  = explode("\n", $output);
$output = '';
foreach ($lines as $line) {
    if ($line === '') {
        $output .= "\n";
    } elseif (($line[0] === ' ') && ($line[1] !== ' ')) {
        $output .= substr($line, 1) . "\n";
    } else {
        // Remove trailing spaces
        $output .= rtrim($line). "\n";
    }
}
// Add the list of the external links at the end of the document
if(!empty(RstConvert::$links)) {
    $output .= "\n" . RstConvert::getLinks();
}
if(!empty(RstConvert::$footnote)) {
    $output .= "\n" . join("\n", RstConvert::$footnote);
}

file_put_contents($rstFile, $output);
echo 'Conversion done.' . PHP_EOL;
exit(0);

require_once __DIR__ . '/ZendBin/RstConvert.php';
