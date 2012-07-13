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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
namespace ZendBin;

use Zend\Console;
use Zend\File\ClassFileLocator;
use Zend\Loader\StandardAutoloader;
use Zend\Text\Table;

/*
 * Convert the ZF documentation from DocBook to reStructuredText format
 *
 * Usage:
 * --help|-h        Get usage message
 * --docbook|-d     Docbook file to convert
 * --output|-o      File output in reStructuredText format; By default,
 *                  assumes <docbook>.rst
 */
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
    'output|o-s'  => 'Output dir; if not provided, assumes normalize(<docbook>).rst"',
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
    echo "Error: the docbook file $docbook doesn't exist.\n";
    exit(2);
}

$rstFile = $opts->getOption('o');
if (empty($rstFile)) {
    $rstFile = '.';
}

if (is_dir($rstFile)) {
    $rstFile .= RstConvert::XmlFileNameToRst(basename($docbook));
}

// Load the docbook file (input)
$xml = new \DOMDocument;
$xml->load($docbook);

$xsltFile = __DIR__ . '/doc2rst.xsl';

// Load the XSLT file
$xsl = new \DOMDocument;
if (!file_exists($xsltFile)) {
    echo "The $xsltFile is missing, I cannot procede with the conversion.\n";
    exit(2);
}
$xsl->load($xsltFile);

$proc = new \XSLTProcessor;
$proc->registerPHPFunctions();
$proc->importStyleSheet($xsl);

echo "Writing to $rstFile\n";

$output = $proc->transformToXml($xml);
if (false === $output) {
    echo "Error during the conversion\n";
    exit(2);
}

// remove single spaces in the beginning of new lines
$lines = explode("\n", $output);
$output = '';
foreach ($lines as $line) {
    if (empty($line)) {
        $output .= "\n";
    } elseif (($line[0] === ' ') && ($line[1] !== ' ')) {
        $output .= substr($line, 1) . "\n";
    } else {
        $output .= "$line\n";
    }
}
// Add the list of the external links at the end of the document
$output .= "\n" . RstConvert::getLinks();

file_put_contents($rstFile, $output);
echo "Conversion done.\n";
exit(0);

/**
 * XSLT php:function()
 */
class RstConvert
{
    public static $links = array();

    /**
     * Convert the programlisting tag
     *
     * @param  string $text
     * @param  string $language
     * @return string
     */
    public static function programlisting($text, $language)
    {
        $language = ($language)?:'php';
        $rows   = explode("\n", $text);
        $output = "\n.. code-block:: $language\n    :linenos:\n";
        foreach ($rows as $row) {
            $output .= "    $row\n";
        }
        return $output;
    }

    /**
     * Convert the note tag
     *
     * @param  string $text
     * @return string
     */
    public static function note($text)
    {
        $rows = explode("\n", $text);
        $tot  = count($rows);
        if ('' !== trim($rows[0])) {
            $output = "    **" . trim($rows[0]) . "**\n";
        } else {
            $output = '';
        }
        for ($i=1; $i < $tot; $i++) {
            if ('' !== trim($rows[$i])) {
                $output .= "    {$rows[$i]}\n";
            }
        }
        return $output;
    }

    /**
     * Convert the listitem tag
     *
     * @param  string $text
     * @return string
     */
    public static function listitem($text)
    {
        $rows = explode("\n", $text);
        $output = "\n";
        foreach ($rows as $row) {
            if ('' !== trim($row)) {
                $output .= "    - ". trim($row) . "\n";
            }
        }
        $output .= "\n";
        return $output;
    }

    /**
     * Convert the first section/title tag (maintitle)
     *
     * @param  string $text
     * @return string
     */
    public static function maintitle($text)
    {
        $text    = str_replace('\\', '\\\\', trim($text));
        $count   = strlen($text);
        $output  = $text . "\n";
        $output .= str_repeat('=', $count) . "\n";
        return $output;
    }

    /**
     * Convert all the section/title, except for the first
     *
     * @param  string $text
     * @return string
     */
    public static function title($text)
    {
        $text    = str_replace('\\', '\\\\', trim($text));
        $count   = strlen($text);
        $output  = "\n" . $text . "\n";
        $output .= str_repeat('-', $count) . "\n";
        return $output;
    }

    /**
     * Format the string removing \n, multiple white spaces and \ in \\
     *
     * @param  string $text
     * @return string
     */
    public static function formatText($text) {
        return str_replace('\\', '\\\\', preg_replace('/\s+/m', ' ', preg_replace('/(([\.:])|^\s([A-Z0-9]))\s*[\r\n]\s*$/', '$2$3', $text)));
    }

    /**
     * Convert the link tag
     *
     * @param  \DOMElement $node
     * @return string
     */
    public static function link($node)
    {
        $value = trim(self::formatText($node[0]->nodeValue));
        if ($node[0]->getAttribute('linkend')) {
            return ":ref:`$value <" . $node[0]->getAttribute('linkend') . ">`";
        } else {
            self::$links[$value] = trim($node[0]->getAttribute('xlink:href'));
            return "`$value`_";
        }
    }

    /**
     * Get all the external links of the document
     *
     * @return string
     */
    public static function getLinks()
    {
        $output = '';
        foreach (self::$links as $key => $value) {
            $output .= ".. _`$key`: $value\n";
        }
        return $output;
    }

    /**
     * Convert the table tag
     *
     * @param  \DOMElement $node
     * @return string
     */
    public static function table($node)
    {
        // check if thead exists
        if (0 !== $node[0]->getElementsByTagName('thead')->length) {
            $head = true;
        } else {
            $head = false;
        }
        $rows   = $node[0]->getElementsByTagName('row');
        $table  = array();
        $totRow = $rows->length;
        $j      = 0;
        foreach ($rows as $row) {
            $cols   = $row->getElementsByTagName('entry');
            $totCol = $cols->length;
            if (!isset($widthCol)) {
                $widthCol = array_fill(0, $totCol, 0);
            }
            $i = 0;
            foreach ($cols as $col) {
                $table[$j][$i] = self::formatText($col->nodeValue);
                $length = strlen($table[$j][$i]);
                if ($length > $widthCol[$i]) {
                    $widthCol[$i] = $length;
                }
                $i++;
            }
            $j++;
        }
        $tableText = new Table\Table(array(
            'columnWidths' => $widthCol,
            'decorator'    => 'ascii'
        ));
        for ($j=0; $j < $totRow; $j++) {
            $row = new Table\Row();
            for ($i=0; $i < $totCol; $i++) {
                $row->appendColumn(new Table\Column($table[$j][$i]));
            }
            $tableText->appendRow($row);
        }
        $output = $tableText->render();
        // if thead exists change the table style with head (= instead of -)
        if ($head) {
            $table  = explode("\n", $output);
            $newOutput = '';
            $i      = 0;
            foreach ($table as $row) {
                if ('+-' === substr($row, 0, 2)) {
                    $i++;
                }
                if (2 === $i) {
                    $row = str_replace('-', '=', $row);
                }
                $newOutput .= "$row\n";
            }
            return $newOutput;
        }
        return $output;
    }

    /**
     * Convert an XML file name to the RST ZF2 standard naming convention
     * For instance, Zend_Config-XmlIntro.xml become zend.config.xml-intro.rst
     *
     * @param  string $name
     * @return string
     */
    public static function XmlFileNameToRst($name)
    {
        if ('.xml' === strtolower(substr($name, -4))) {
            $name = substr($name, 0, strlen($name)-4);
        }
        $tot = strlen($name);
        $output = '';
        $word = false;
        for ($i=0; $i < $tot; $i++) {

            if (preg_match('/[A-Z]/', $name[$i])) {
                if ($word) {
                    $output .= '-';
                }
                $output .= strtolower($name[$i]);
            } elseif ('_' === $name[$i] || '-' === $name[$i]) {
                $output .= '.';
                $word = false;
            } else {
                $output .= $name[$i];
                $word = true;
            }
        }
        return $output.'.rst';
    }
}
