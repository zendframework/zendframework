<?php
$libPath = __DIR__ . '/../library';
if (!is_dir($libPath)) {
    echo "Unable to find Zend Framework library; aborting" . PHP_EOL;
    exit(2);
}
$libPath  = realpath($libPath);
$filePath = $libPath . '/Zend/_autoload.php';

if ($argc && $argc > 1) {
    // Did we get a path to the autoload file? 
    // If so, make sure we can write to it.
    $filePath = $argv[1];
    if (!is_writeable(dirname($filePath))) {
        echo "Unable to write to $filePath; aborting" . PHP_EOL;
        exit(2);
    }
}

if (file_exists($filePath)) {
    // Does the specified autoload file exist?
    // If so, check to see if the "overwrite" flag was provided.
    // If it wasn't, abort, and tell the user why.
    $filePath = realpath($filePath);
    if ($argc && $argc < 3) {
        echo "Autoload file already exists at location $filePath." . PHP_EOL
            . "Append the switch --overwrite or -o to overwrite:" . PHP_EOL
            . "    ". $argv[0] . ' ' . $filePath . ' --overwrite' . PHP_EOL
            . "    ". $argv[0] . ' ' . $filePath . ' -o' . PHP_EOL;
        exit(2);
    }
    if (!in_array($argv[2], array('--overwrite', '-o'))) {
        echo "Autoload file already exists at location $filePath," . PHP_EOL
            . "and unknown switch provided." . PHP_EOL;
        exit(2);
    }
}

// Add ZF to the include_path, if it isn't already
$incPath = get_include_path();
if (!strstr($incPath, $libPath)) {
    set_include_path($libPath . PATH_SEPARATOR . $incPath);
}

// Setup autoloading
require_once 'Zend/Loader/Autoloader.php';
Zend\Loader\Autoloader::getInstance();

// Get the ClassFileLocater, and pass it the library path
$l = new \Zend\File\ClassFileLocater($libPath);

// Iterate over each element in the path, and create a map of 
// classname => filename, where the filename is relative to the library path
$map   = array();
iterator_apply($l, function(\Iterator $it, $strip, array $map) {
    $file      = $it->current();
    $namespace = empty($file->namespace) ? '' : $file->namespace . '\\';
    $filename  = str_replace($strip, '', $file->getRealpath());

    $map[$namespace . $file->classname] = $filename;

    return true;
}, array($l, $libPath . DIRECTORY_SEPARATOR, &$map));

// Create a file with the class/file map.
// Stupid syntax highlighters make separating < from PHP declaration necessary
$content = '<' . "?php\n"
         . 'return ' . var_export($map, true) . ';';
file_put_contents($libPath . '/Zend/_autoload.php', $content);

echo "Wrote autoload file to " . realpath($filePath) . PHP_EOL;
