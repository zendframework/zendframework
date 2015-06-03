#!/usr/bin/env php
<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

use Zend\Console;
use Zend\Loader\StandardAutoloader;

/**
 * Generate template maps.
 *
 * Usage:
 * --help|-h                    Get usage message
 * --library|-l [ <string> ]    Library to parse; if none provided, assumes
 *                              current directory
 * --view|-v [ <string> ]       View path to parse; if none provided, assumes
 *                              view as template directory
 * --extensions|-e [ <string> ] List of accepted file extensions (regex alternation
 *                              without parenthesis); default: *
 * --output|-o [ <string> ]     Where to write map file; if not provided,
 *                              assumes "template_map.php" in library directory
 * --append|-a                  Append to map file if it exists
 * --overwrite|-w               Whether or not to overwrite existing map file
 */

// Setup/verify autoloading
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    // Local install
    require __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(getcwd() . '/vendor/autoload.php')) {
    // Root project is current working directory
    require getcwd() . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../autoload.php')) {
    // Relative to composer install
    require __DIR__ . '/../../../autoload.php';
} else {
    fwrite(STDERR, "Unable to setup autoloading; aborting\n");
    exit(2);
}

$libraryPath = getcwd();
$viewPath    = getcwd() . '/view';

$rules = array(
    'help|h'            => 'Get usage message',
    'library|l-s'       => 'Library to parse; if none provided, assumes current directory',
    'view|v-s'          => 'View path to parse; if none provided, assumes view as template directory',
    'extensions|e-s'    => 'List of accepted file extensions (regex alternation: *html, phtml|tpl); default: *',
    'output|o-s'        => 'Where to write map file; if not provided, assumes "template_map.php" in library directory',
    'append|a'          => 'Append to map file if it exists',
    'overwrite|w'       => 'Whether or not to overwrite existing map file',
);

try {
    $opts = new Console\Getopt($rules);
    $opts->parse();
} catch (Console\Exception\RuntimeException $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if ($opts->getOption('h')) {
    echo $opts->getUsageMessage();
    exit(0);
}

$fileExtensions = '*';
if (isset($opts->e) && $opts->e != '*') {
    if (!preg_match('/^(\*?[[:alnum:]]\*?+\|?)+$/', $opts->e)) {
        echo 'Invalid extensions list specified. Expecting wildcard or alternation: *, *html, phtml|tpl' . PHP_EOL
            . PHP_EOL;
        echo $opts->getUsageMessage();
        exit(2);
    }
    $fileExtensions = '(' . $opts->e . ')';
}
$fileExtensions = str_replace('*', '.*', $fileExtensions);

$relativePathForMap = '';
if (isset($opts->l)) {
    if (!is_dir($opts->l)) {
        echo 'Invalid library directory provided' . PHP_EOL
            . PHP_EOL;
        echo $opts->getUsageMessage();
        exit(2);
    }
    $libraryPath = $opts->l;
}
$libraryPath = str_replace(DIRECTORY_SEPARATOR, '/', realpath($libraryPath));

if (isset($opts->v)) {
    if (!is_dir($opts->v)) {
        echo 'Invalid view template directory provided' . PHP_EOL
            . PHP_EOL;
        echo $opts->getUsageMessage();
        exit(2);
    }
    $viewPath = $opts->v;
}

if (!is_dir($viewPath)) {
    printf('Invalid view path provided (%s)', $viewPath);
    echo PHP_EOL . PHP_EOL;
    echo $opts->getUsageMessage();
    exit(2);
}

$viewPath = str_replace(DIRECTORY_SEPARATOR, '/', realpath($viewPath));

$usingStdout = false;
$appending   = $opts->getOption('a');
$output      = $libraryPath . '/template_map.php';
if (isset($opts->o)) {
    $output = $opts->o;
    if ('-' == $output) {
        $output = STDOUT;
        $usingStdout = true;
    } elseif (is_dir($output)) {
        echo 'Invalid output file provided' . PHP_EOL
            . PHP_EOL;
        echo $opts->getUsageMessage();
        exit(2);
    } elseif (!is_writeable(dirname($output))) {
        echo "Cannot write to '$output'; aborting." . PHP_EOL
            . PHP_EOL
            . $opts->getUsageMessage();
        exit(2);
    } elseif (file_exists($output) && !$opts->getOption('w') && !$appending) {
        echo "Template map file already exists at '$output'," . PHP_EOL
            . "but 'overwrite' or 'appending' flag was not specified; aborting." . PHP_EOL
            . PHP_EOL
            . $opts->getUsageMessage();
        exit(2);
    } else {
        // We need to add the $libraryPath into the relative path that is created in the template map file.
        $mapPath = str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname($output)));

        // Simple case: $libraryPathCompare is in $mapPathCompare
        if (strpos($libraryPath, $mapPath) === 0) {
            $relativePathForMap = substr($libraryPath, strlen($mapPath) + 1) . '/';
        } else {
            $libraryPathParts  = explode('/', $libraryPath);
            $mapPathParts = explode('/', $mapPath);

            // Find the common part
            $count = count($mapPathParts);
            for ($i = 0; $i < $count; $i++) {
                if (!isset($libraryPathParts[$i]) || $libraryPathParts[$i] != $mapPathParts[$i]) {
                    // Common part end
                    break;
                }
            }

            // Add parent dirs for the subdirs of map
            $relativePathForMap = str_repeat('../', $count - $i);

            // Add library subdirs
            $count = count($libraryPathParts);
            for (; $i < $count; $i++) {
                $relativePathForMap .= $libraryPathParts[$i] . '/';
            }
        }
    }
}

if (!$usingStdout) {
    if ($appending) {
        echo "Appending to template file map '$output' for library in '$libraryPath'..." . PHP_EOL;
    } else {
        echo "Creating template file map for library in '$libraryPath'..." . PHP_EOL;
    }
}

$dirOrIterator = new RecursiveDirectoryIterator($viewPath, RecursiveDirectoryIterator::FOLLOW_SYMLINKS);
$l = new RecursiveIteratorIterator($dirOrIterator);

// Iterate over each element in the path, and create a map of
// template name => filename, where the filename is relative to the view path
$map = new stdClass;
foreach ($l as $file) {
    /* @var $file SplFileInfo */
    if (!$file->isFile() || !preg_match('/^' . $fileExtensions . '$/', $file->getExtension())) {
        continue;
    }
    $filename  = str_replace($libraryPath . '/', '', str_replace(DIRECTORY_SEPARATOR, '/', $file->getPath()) . '/' . $file->getFilename());

    // Add in relative path to library
    $filename = $relativePathForMap . $filename;
    $baseName = $file->getBasename('.' . pathinfo($file->getFilename(), PATHINFO_EXTENSION));
    $mapName  = str_replace(str_replace(DIRECTORY_SEPARATOR, '/', realpath($viewPath)) . '/', '', str_replace(DIRECTORY_SEPARATOR, '/', $file->getPath()) . '/' . $baseName);
    $map->{$mapName} = $filename;
}

// Create a file with the map.

if ($appending && file_exists($output) && is_array(include $output)) {
    // Append mode and the output file already exists: retrieve its
    // content and merges with the new map
    // Remove the last line as it is the end of the array, and we want to
    // append our new templates
    $content = file($output, FILE_IGNORE_NEW_LINES);
    array_pop($content);
    $content = implode(PHP_EOL, $content) . PHP_EOL;
} else {
    // Write mode or the file does not exists: create a new file
    // Stupid syntax highlighters make separating < from PHP declaration necessary
    $content = '<' . "?php" . PHP_EOL
             . '// Generated by ZF2\'s ./bin/templatemap_generator.php'  . PHP_EOL
             . 'return array(' . PHP_EOL;
}

// Process the template map as a string before inserting it to the output file

$mapExport = var_export((array) $map, true);

// Prefix with __DIR__
$mapExport = preg_replace("#(=> ')#", "=> __DIR__ . '/", $mapExport);

// Fix \' strings from injected DIRECTORY_SEPARATOR usage in iterator_apply op
$mapExport = str_replace("\\'", "'", $mapExport);

// Remove unnecessary double-backslashes
$mapExport = str_replace('\\\\', '\\', $mapExport);

// Remove "array ("
$mapExport = str_replace('array (', '', $mapExport);

// Align "=>" operators to match coding standard
preg_match_all('(\n\s+([^=]+)=>)', $mapExport, $matches, PREG_SET_ORDER);
$maxWidth = 0;

foreach ($matches as $match) {
    $maxWidth = max($maxWidth, strlen($match[1]));
}

$mapExport = preg_replace_callback('(\n\s+([^=]+)=>)', function ($matches) use ($maxWidth) {
    return PHP_EOL . '    ' . $matches[1] . str_repeat(' ', $maxWidth - strlen($matches[1])) . '=>';
}, $mapExport);

// Trim the content
$mapExport = trim($mapExport, "\n");

// Append the map to the file, close the array and write a new line
$content .= $mapExport . ';' . PHP_EOL;

// Write the contents to disk
file_put_contents($output, $content);

if (!$usingStdout) {
    echo "Wrote templatemap file to '" . realpath($output) . "'" . PHP_EOL;
}
