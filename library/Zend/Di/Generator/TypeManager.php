<?php

namespace Zend\Di\Generator;

class TypeManager
{
    /**
     * @var ClassRegistry
     */
    protected $typeRegistry = null;
    protected $managedNamespaces = array();
    //protected $directoryIterators = null;
    
    public function __construct(TypeRegistry $typeRegistry, array $namespaces = array(), array $directories = array())
    {
        $this->typeRegistry = $typeRegistry;
        
        // create directory iterator basd on namespace
        $includePaths = explode(PATH_SEPARATOR, get_include_path());
        foreach ($namespaces as $namespace) {
            $this->addNamespace($namespace);
        }
        
        // create directory iterator based on explicit directory
        foreach ($directories as $directory) {
            $this->addDirectory($directory);
        }
    }
    
    public function addNamespace($namespace)
    {
        $namespaceAsDirectory = ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $namespace), DIRECTORY_SEPARATOR);
        if (($directory = stream_resolve_include_path($namespaceAsDirectory)) === false) {
            throw new \Exception('Namespace not located in include_path');
        } else {
            $this->managedNamespaces[$directory] = $namespace;
            //$this->directoryIterators[] = new \RecursiveDirectoryIterator($directory);
        }
    }
    
    
    /**
     * There is an interesting problem with managing directories.  Classes need to be handled by autoloading,
     * since when they are reflected later, will need to be autoloaded if they are not known.  In some cases
     * we will have outside types that are not managed by the DI system that are dependencies.  We do not
     * want to autoload these since they are not inside our managed namespace..
     */
    
//    public function addDirectory($directory)
//    {
//        if (!file_exists($directory)) {
//            throw new \Exception('Directory ' . $directory . ' not found');
//        } else {
//            $this->directoryIterators[] = new \RecursiveDirectoryIterator($directory);
//        }
//    }
    
    
    /**
	 * 
     */
    public function manage()
    {
        if ($this->managedNamespaces == null) {
            throw new \Exception('Nothing to manage.');
        }
        
        $currentNamespace = null;
        
        // convert namespace to directory iterator, but first, create a namespace only autoloader.
        $nsAutoloader = function($class) use (&$currentNamespace) {
            if (strpos($class, $currentNamespace) !== 0) return;
            $file = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $class) . '.php';
            return (false !== ($file = stream_resolve_include_path($file))) ? include_once($file) : false;
        };
        
        spl_autoload_register($nsAutoloader);
        
        //foreach ($this->directoryIterators as $dirIter) {
        foreach ($this->managedNamespaces as $directory => $currentNamespace) {
            $directoryIterator = new \RecursiveDirectoryIterator($directory);
            $rii = new \RecursiveIteratorIterator($directoryIterator); //, $mode, $flags); 
            foreach ($rii as $item) {
                if (!$rii->isDot() && $item->getType() == 'file' && preg_match('#\.php$#', $item->getFilename())) {

                    /**
                     * @todo Short-circuit here is fmtime has not changed and is inside the ClassRegistry
                     */

                    $classes = get_declared_classes();
                    $interfaces = get_declared_interfaces();
                    require_once $item->getPathname();
                    foreach (array_values(array_diff(get_declared_classes(), $classes)) as $class) {
                        $this->typeRegistry->register($class, $item->getPathname(), $item->getMtime());
                    }
                    foreach (array_values(array_diff(get_declared_interfaces(), $interfaces)) as $interface) {
                        $this->typeRegistry->register($interface, $item->getPathname(), $item->getMtime());
                    }
                }
            }
        }
        
        spl_autoload_unregister($nsAutoloader);
        
    }

}
