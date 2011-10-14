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
require_once 'Zend/Loader/StandardAutoloader.php';

$loader = new Zend\Loader\StandardAutoloader();
$loader->register();

$rules = array(
    'help|h'        => 'Get usage message',
    'library|l-s'   => 'Library to parse; if none provided, assumes current directory',
    'namespace|n-s' => 'Namespace in which to create map; by default, uses last segment of library directory name',
    'output|o-s'    => 'Where to write autoload file; if not provided, assumes "_autoload.php" in library directory',
    'overwrite|w'   => 'Whether or not to overwrite existing autoload file',
    'keepdepth|k-i' => 'How many additional segments of the library path to keep in the generated classfile map',
    'usedir|d'      => 'Prepend filenames with __DIR__',
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
if (isset($opts->l)) {
    $path = $opts->l;
    if (!is_dir($path)) {
        echo "Invalid library directory provided" . PHP_EOL . PHP_EOL;
        echo $opts->getUsageMessage();
        exit(2);
    }
    $path = realpath($path);
}

$namespace = substr($path, strrpos($path, DIRECTORY_SEPARATOR) + 1);
if (isset($opts->n)) {
    $tmp = $opts->n;
    if (!empty($tmp)) {
        if (!preg_match('#^[a-z][a-z0-9]*(\\\\[a-z][a-z[0-9_]])*#', $tmp)) {
            echo "Invalid namespace provided; aborting." . PHP_EOL
                . PHP_EOL
                . $opts->getUsageMessage();
            exit(2);
        }
        $namespace = $tmp;
    }
}

$usingStdout = false;
$output = $path . DIRECTORY_SEPARATOR . '_autoload.php';
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
        if (!$opts->getOption('w')) {
            echo "Autoload file already exists at '$output'," . PHP_EOL
                . "but 'overwrite' flag was not specified; aborting." . PHP_EOL 
                . PHP_EOL
                . $opts->getUsageMessage();
            exit(2);
        }
    }
}

$strip     = $path;
$keepDepth = 0;
if (isset($opts->k)) {
    $keepDepth = $opts->k;
    if ($keepDepth < 0) {
        $keepDepth = 0;
    }
}
if ($keepDepth > 0) {
    $segments = explode(DIRECTORY_SEPARATOR, $path);
    do {
        array_pop($segments);
        --$keepDepth;
    } while (count($segments) > 0 && $keepDepth > 0);
    $strip = implode(DIRECTORY_SEPARATOR, $segments);
}

$prefixWithDir = $opts->getOption('d');

if (!$usingStdout) {
    echo "Creating class file map for library in '$path'..." . PHP_EOL;
}

// Get the ClassFileLocator, and pass it the library path
$l = new \Zend\File\ClassFileLocator($path);

// Iterate over each element in the path, and create a map of 
// classname => filename, where the filename is relative to the library path
$map    = new \stdClass;
$strip .= DIRECTORY_SEPARATOR;
foreach ($l as $file) {
    $namespace = empty($file->namespace) ? '' : $file->namespace . '\\';
    $filename  = str_replace($strip, '', $file->getRealPath());

    $map->{$namespace . $file->classname} = $filename;
}

// Create a file with the class/file map.
// Stupid syntax highlighters make separating < from PHP declaration necessary
$map     = var_export((array) $map, true);
$content =<<<EOT
<?php
namespace $namespace;
\$_map = $map;
spl_autoload_register(function(\$class) use (\$_map) {
    if (array_key_exists(\$class, \$_map)) {
        require_once \$_map[\$class];
    }
});
EOT;

// If requested to prefix with __DIR__, modify the content
if ($prefixWithDir) {
    $content = preg_replace('#(=> )#', '$1__DIR__ . DIRECTORY_SEPARATOR . ', $content);
}
file_put_contents($output, $content);

if (!$usingStdout) {
    echo "Wrote autoload file to '" . realpath($output) . "'" . PHP_EOL;
}
