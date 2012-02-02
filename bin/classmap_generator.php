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
 * @package    Zend_Loader
 * @subpackage Exception
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Generate class maps for use with autoloading.
 *
 * Usage:
 * --help|-h                    Get usage message
 * --library|-l [ <string> ]    Library to parse; if none provided, assumes 
 *                              current directory
 * --output|-o [ <string> ]     Where to write autoload file; if not provided, 
 *                              assumes "autoload_classmap.php" in library directory
 * --append|-a                  Append to autoload file if it exists
 * --overwrite|-w               Whether or not to overwrite existing autoload 
 *                              file
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
$loader = new Zend\Loader\StandardAutoloader();
$loader->register();

$rules = array(
    'help|h'        => 'Get usage message',
    'library|l-s'   => 'Library to parse; if none provided, assumes current directory',
    'output|o-s'    => 'Where to write autoload file; if not provided, assumes "autoload_classmap.php" in library directory',
    'append|a'      => 'Append to autoload file if it exists',
    'overwrite|w'   => 'Whether or not to overwrite existing autoload file',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

if ($opts->getOption('h')) {
    echo $opts->getUsageMessage();
    exit();
}

$path = $libPath;
if (array_key_exists('PWD', $_SERVER)) {
    $path = $_SERVER['PWD'];
}

$relativePathForClassmap = '';
if (isset($opts->l)) {
    $libraryPath = $opts->l;
    $libraryPath = str_replace('\\', '/', rtrim($libraryPath, '/\\')) . '/';
    if (!is_dir($libraryPath)) {
        echo "Invalid library directory provided" . PHP_EOL . PHP_EOL;
        echo $opts->getUsageMessage();
        exit(2);
    }
    $path = str_replace('\\', '/', realpath($libraryPath));
    
    // If -o has been used, then we need to add the $libraryPath into the relative 
    // path that is created in the classmap file.
    if ($opts->o != '') {
        // If both library path and classmap path are absolute, we have to make
        // it relative to the classmap file.
        $libraryPathCompare  = rtrim(str_replace('\\', '/', realpath($libraryPath)), '/');

        if (file_exists($opts->o) ) {
            $classmapPathCompare = rtrim(str_replace('\\', '/', realpath($opts->o)), '/');
        } else {
            // realpath() won't work for unexisting files
            $newFilePath = explode('/', str_replace('\\', '/', $opts->o));
            // stip filename
            array_pop($newFilePath);
            $classmapPathCompare = rtrim(realpath(implode('/', $newFilePath)), '/');
        }

        if (is_file($libraryPathCompare)) {
            $libraryPathCompare = str_replace('\\', '/', dirname($libraryPathCompare));
        }
        
        if (is_file($classmapPathCompare)) {
            $classmapPathCompare = str_replace('\\', '/', dirname($classmapPathCompare));
        }

        // Simple case: $libraryPathCompare is in $classmapPathCompare
        if (strpos($libraryPathCompare, $classmapPathCompare) === 0) {
            $relativePathForClassmap = substr($libraryPathCompare, strlen($classmapPathCompare) + 1) . '/';
        } else {
            $relative          = array();
            $libraryPathParts  = explode('/', $libraryPathCompare);
            $classmapPathParts = explode('/', $classmapPathCompare);

            foreach ($classmapPathParts as $index => $part) {
                var_dump($libraryPathParts[$index]);
                var_dump($part);
                var_dump(1);
                
                if (isset($libraryPathParts[$index]) && $libraryPathParts[$index] == $part) {
                    continue;
                }

                $relative[] = '..';
            }

            foreach ($libraryPathParts as $index => $part ) {
                if (isset($classmapPathParts[$index]) && $classmapPathParts[$index] == $part) {
                    continue;
                }

                $relative[] = $part;
            }

            $relativePathForClassmap = implode('/', $relative) . '/';
        }
    }
}

$usingStdout = false;
$appending = $opts->getOption('a');
$output = $path . '/autoload_classmap.php';
if (isset($opts->o)) {
    $output = $opts->o;
    if ('-' == $output) {
        $output = STDOUT;
        $usingStdout = true;
    } elseif (!is_writeable(dirname($output))) {
        echo "Cannot write to '$output'; aborting." . PHP_EOL
            . PHP_EOL
            . $opts->getUsageMessage();
        exit(2);
    } elseif (file_exists($output)) {
        if (!$opts->getOption('w') && !$appending) {
            echo "Autoload file already exists at '$output'," . PHP_EOL
                . "but 'overwrite' flag was not specified; aborting." . PHP_EOL 
                . PHP_EOL
                . $opts->getUsageMessage();
            exit(2);
        }
    }
}

$strip = $path;

if (!$usingStdout) {
    if ($appending) {
        echo "Appending to class file map '$output' for library in '$path'..." . PHP_EOL;
    } else {
        echo "Creating class file map for library in '$path'..." . PHP_EOL;
    }
}

// Get the ClassFileLocator, and pass it the library path
$l = new \Zend\File\ClassFileLocator($path);

// Iterate over each element in the path, and create a map of 
// classname => filename, where the filename is relative to the library path
$map    = new \stdClass;
$strip .= '/';
foreach ($l as $file) {
    $namespace = empty($file->namespace) ? '' : $file->namespace . '\\';
    $filename  = str_replace($strip, '', str_replace('\\', '/', $file->getPath()) . '/' . $file->getFilename());

    // Add in relative path to library
    $filename  = $relativePathForClassmap . $filename;

    // Replace directory separators with forward slash
    $filename  = str_replace(array('/', '\\'), '/', $filename);

    $map->{$namespace . $file->classname} = $filename;
}

if ($appending) {
    $content = var_export((array) $map, true) . ';';

    // Prefix with __DIR__; modify the generated content
    $content = preg_replace("#(=> ')#", "=> __DIR__ . '/", $content);

    // Fix \' strings from injected DIRECTORY_SEPARATOR usage in iterator_apply op
    $content = str_replace("\\'", "'", $content);

    // Convert to an array and remove the first "array("
    $content = explode(PHP_EOL, $content);
    array_shift($content);

    // Load existing class map file and remove the closing "bracket ");" from it
    $existing = file($output, FILE_IGNORE_NEW_LINES);
    array_pop($existing); 

    // Merge
    $content = implode(PHP_EOL, array_merge($existing, $content));
} else {
    // Create a file with the class/file map.
    // Stupid syntax highlighters make separating < from PHP declaration necessary
    $content = '<' . "?php\n"
             . "// Generated by ZF2's ./bin/classmap_generator.php\n"
             . 'return ' . var_export((array) $map, true) . ';';

    // Prefix with __DIR__; modify the generated content
    $content = preg_replace("#(=> ')#", "=> __DIR__ . '/", $content);

    // Fix \' strings from injected DIRECTORY_SEPARATOR usage in iterator_apply op
    $content = str_replace("\\'", "'", $content);
}

// Remove unnecessary double-backslashes
$content = str_replace('\\\\', '\\', $content);

// Exchange "array (" width "array("
$content = str_replace('array (', 'array(', $content);

// Allign "=>" operators to match coding standard
preg_match_all('(\n\s+([^=]+)=>)', $content, $matches, PREG_SET_ORDER);
$maxWidth = 0;

foreach ($matches as $match) {
    $maxWidth = max($maxWidth, strlen($match[1]));
}

$content = preg_replace('(\n\s+([^=]+)=>)e', "'\n    \\1' . str_repeat(' ', " . $maxWidth . " - strlen('\\1')) . '=>'", $content);

// Write the contents to disk
file_put_contents($output, $content);

if (!$usingStdout) {
    echo "Wrote classmap file to '" . realpath($output) . "'" . PHP_EOL;
}
