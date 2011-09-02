<?php
/*
 * Report all errors
 */
ini_set('display_errors', true);
error_reporting(-1);

/*
 * Add this directory and the src directory to the include_path.
 */
set_include_path(implode(PATH_SEPARATOR, array(
    '.',
    __DIR__ . '/../src',
    get_include_path(),
)));

/**
 * Setup autoloading
 */
spl_autoload_register(function($classname) {
    $classname = ltrim($classname, '\\');
    $filename  = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $classname)
               . '.php';
    $realpath  = stream_resolve_include_path($filename);
    if (!$realpath) {
        return false;
    }
    return include_once($realpath);
});
