<?php
$libPath = __DIR__ . '/../library';
if (!is_dir($libPath)) {
    echo "Unable to find Zend Framework library; aborting" . PHP_EOL;
    exit(2);
}
$libPath  = realpath($libPath);

// Add ZF to the include_path, if it isn't already
$incPath = get_include_path();
if (!strstr($incPath, $libPath)) {
    set_include_path($libPath . PATH_SEPARATOR . $incPath);
}

// Setup autoloading
require_once 'Zend/Loader/Autoloader.php';
Zend\Loader\Autoloader::getInstance();

$rules = array(
    'library|l-s' => 'Library to parse; if none provided, assumes current directory',
    'output|o-s'  => 'Where to write autoload file; if not provided, assumes "_autoload.php" in library directory',
    'overwrite|w' => 'Whether or not to overwrite existing autoload file',
    'keepdepth|k-i' => 'How many additional segments of the library path to keep in the generated classfile map',
);

try {
    $opts = new Zend\Console\Getopt($rules);
    $opts->parse();
} catch (Zend\Console\Getopt\Exception $e) {
    echo $e->getUsageMessage();
    exit(2);
}

$path = $libPath;
if (array_key_exists('PWD', $_SERVER)) {
    $path = $_SERVER['PWD'];
}
if (isset($opts->l)) {
    $path = $opts->l;
    if (!is_dir($path)) {
        echo "Invalid library directory provided" . PHP_EOL . PHP_EOL;
        echo $opts->getUsageMessage();
        exit(2);
    }
    $path = realpath($path);
}

$output = $path . DIRECTORY_SEPARATOR . '_autoload.php';
if (isset($opts->o)) {
    $output = $opts->o;
    if ('-' == $output) {
        $output = STDOUT;
    } elseif (!is_writeable(dirname($output))) {
        echo "Cannot write to '$output'; aborting." . PHP_EOL
            . PHP_EOL
            . $opts->getUsageMessage();
        exit(2);
    } elseif (file_exists($output)) {
        if (!$opts->getOption('w')) {
            echo "Autoload file already exists at '$output'," . PHP_EOL
                . "but 'overwrite' flag was not specified; aborting." . PHP_EOL 
                . PHP_EOL
                . $opts->getUsageMessage();
            exit(2);
        }
    }
}

$strip     = '';
$keepDepth = 1;
if (isset($opts->k)) {
    $keepDepth = $opts->k;
    if ($keepDepth < 0) {
        $keepDepth = 0;
    }
}
if ($keepDepth) {
    $segments = explode(DIRECTORY_SEPARATOR, $path);
    do {
        array_pop($segments);
        --$keepDepth;
    } while (count($segments) > 0 && $keepDepth > 0);
    $strip = implode(DIRECTORY_SEPARATOR, $segments);
}

// Get the ClassFileLocater, and pass it the library path
$l = new \Zend\File\ClassFileLocater($path);

// Iterate over each element in the path, and create a map of 
// classname => filename, where the filename is relative to the library path
$map   = array();
iterator_apply($l, function(\Iterator $it, $strip, array $map) {
    $file      = $it->current();
    $namespace = empty($file->namespace) ? '' : $file->namespace . '\\';
    $filename  = str_replace($strip, '', $file->getRealpath());

    $map[$namespace . $file->classname] = $filename;

    return true;
}, array($l, $strip . DIRECTORY_SEPARATOR, &$map));

// Create a file with the class/file map.
// Stupid syntax highlighters make separating < from PHP declaration necessary
$content = '<' . "?php\n"
         . 'return ' . var_export($map, true) . ';';
file_put_contents($output, $content);

echo "Wrote autoload file to " . realpath($output) . PHP_EOL;
