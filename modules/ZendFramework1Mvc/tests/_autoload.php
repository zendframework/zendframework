<?php
/**
 * Setup autoloading
 */
function ZendTest_Autoloader($class) 
{
    $class = ltrim($class, '\\');

    if (!preg_match('#^(Zend(Test)?|PHPUnit)(\\\\|_)#', $class)) {
        return false;
    }

    $segments = preg_split('#[\\\\_]#', $class); // preg_split('#\\\\|_#', $class);//
    $ns       = array_shift($segments);

    switch ($ns) {
        case 'Zend':
            $dirs = array(
                __DIR__ . '/../library/Zend/',
                __DIR__ . '/../../../library/Zend',
            );
            break;
        case 'ZendTest':
            // temporary fix for ZendTest namespace until we can migrate files 
            // into ZendTest dir
            $dirs= array(
                __DIR__ . '/Zend/',
                __DIR__ . '/../../../tests/Zend',
            );
            break;
        default:
            $dirs= false;
            break;
    }

    if ($dirs) {
        foreach ($dirs as $dir) {
            $file = $dir . '/' . implode('/', $segments) . '.php';
            if (file_exists($file)) {
                return include_once $file;
            }
        }
    }

    $segments = explode('_', $class);
    $ns       = array_shift($segments);

    switch ($ns) {
        case 'Zend':
            $dirs = array(
                __DIR__ . '/../library/Zend/',
                __DIR__ . '/../../../library/Zend',
            );
            break;
            break;
        default:
            return false;
    }

    if ($dirs) {
        foreach ($dirs as $dir) {
            $file = $dir . '/' . implode('/', $segments) . '.php';
            if (file_exists($file)) {
                return include_once $file;
            }
        }
    }

    return false;
}
spl_autoload_register('ZendTest_Autoloader', true, true);

