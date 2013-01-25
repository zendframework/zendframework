<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace Zend\Code\Scanner;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Zend\Code\Exception;

class DirectoryScanner implements ScannerInterface
{
    /**
     * @var bool
     */
    protected $isScanned = false;

    /**
     * @var string[]|DirectoryScanner[]
     */
    protected $directories = array();

    /**
     * @var FileScanner[]
     */
    protected $fileScanners = array();

    /**
     * @var array
     */
    protected $classToFileScanner = null;

    /**
     * Constructor
     *
     * @param null|string|array $directory
     */
    public function __construct($directory = null)
    {
        if ($directory) {
            if (is_string($directory)) {
                $this->addDirectory($directory);
            } elseif (is_array($directory)) {
                foreach ($directory as $d) {
                    $this->addDirectory($d);
                }
            }
        }
    }

    /**
     * Add directory
     *
     * @param DirectoryScanner|string $directory
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function addDirectory($directory)
    {
        if ($directory instanceof DirectoryScanner) {
            $this->directories[] = $directory;
        } elseif (is_string($directory)) {
            $realDir = realpath($directory);
            if (!$realDir || !is_dir($realDir)) {
                throw new Exception\InvalidArgumentException(
                    sprintf('Directory "%s" does not exist', $realDir));
            }
            $this->directories[] = $realDir;
        } else {
            throw new Exception\InvalidArgumentException(
                'The argument provided was neither a DirectoryScanner or directory path'
            );
        }
    }

    /**
     * Add directory scanner
     *
     * @param DirectoryScanner $directoryScanner
     * @return void
     */
    public function addDirectoryScanner(DirectoryScanner $directoryScanner)
    {
        $this->addDirectory($directoryScanner);
    }

    /**
     * Add file scanner
     *
     * @param FileScanner $fileScanner
     * @return void
     */
    public function addFileScanner(FileScanner $fileScanner)
    {
        $this->fileScanners[] = $fileScanner;
    }

    /**
     * Scan
     *
     * @return void
     */
    protected function scan()
    {
        if ($this->isScanned) {
            return;
        }

        // iterate directories creating file scanners
        foreach ($this->directories as $directory) {
            if ($directory instanceof DirectoryScanner) {
                $directory->scan();
                if ($directory->fileScanners) {
                    $this->fileScanners = array_merge($this->fileScanners, $directory->fileScanners);
                }
            } else {
                $rdi = new RecursiveDirectoryIterator($directory);
                foreach (new RecursiveIteratorIterator($rdi) as $item) {
                    if ($item->isFile() && pathinfo($item->getRealPath(), PATHINFO_EXTENSION) == 'php') {
                        $this->fileScanners[] = new FileScanner($item->getRealPath());
                    }
                }
            }
        }

        $this->isScanned = true;
    }

    /**
     * Get namespace
     *
     * @todo implement method
     */
    public function getNamespaces()
    {
        // @todo
    }

    /**
     * Get files
     *
     * @param bool $returnFileScanners
     * @return array
     */
    public function getFiles($returnFileScanners = false)
    {
        $this->scan();

        $return = array();
        foreach ($this->fileScanners as $fileScanner) {
            $return[] = ($returnFileScanners) ? $fileScanner : $fileScanner->getFile();
        }

        return $return;
    }

    /**
     * Get class names
     *
     * @return string[]
     */
    public function getClassNames()
    {
        $this->scan();

        if ($this->classToFileScanner === null) {
            $this->createClassToFileScannerCache();
        }

        return array_keys($this->classToFileScanner);
    }

    /**
     * Get classes
     *
     * @param bool $returnDerivedScannerClass
     * @return string[]
     */
    public function getClasses($returnDerivedScannerClass = false)
    {
        $this->scan();

        if ($this->classToFileScanner === null) {
            $this->createClassToFileScannerCache();
        }

        $returnClasses = array();
        foreach ($this->classToFileScanner as $className => $fsIndex) {
            $classScanner = $this->fileScanners[$fsIndex]->getClass($className);
            if ($returnDerivedScannerClass) {
                $classScanner = new DerivedClassScanner($classScanner, $this);
            }
            $returnClasses[] = $classScanner;
        }

        return $returnClasses;
    }

    /**
     * Check for a class
     *
     * @param string $class
     * @return bool
     */
    public function hasClass($class)
    {
        $this->scan();

        if ($this->classToFileScanner === null) {
            $this->createClassToFileScannerCache();
        }

        return (isset($this->classToFileScanner[$class]));
    }

    /**
     * Get class
     *
     * @param string $class
     * @param bool $returnDerivedScannerClass
     * @return ClassScanner|DerivedClassScanner
     * @throws Exception\InvalidArgumentException
     */
    public function getClass($class, $returnDerivedScannerClass = false)
    {
        $this->scan();

        if ($this->classToFileScanner === null) {
            $this->createClassToFileScannerCache();
        }

        if (!isset($this->classToFileScanner[$class])) {
            throw new Exception\InvalidArgumentException('Class not found.');
        }

        /** @var FileScanner $fs */
        $fs          = $this->fileScanners[$this->classToFileScanner[$class]];
        $returnClass = $fs->getClass($class);

        if (($returnClass instanceof ClassScanner) && $returnDerivedScannerClass) {
            return new DerivedClassScanner($returnClass, $this);
        }

        return $returnClass;
    }

    /**
     * Create class to file scanner cache
     *
     * @return void
     */
    protected function createClassToFileScannerCache()
    {
        if ($this->classToFileScanner !== null) {
            return;
        }

        $this->classToFileScanner = array();
        /** @var FileScanner $fileScanner */
        foreach ($this->fileScanners as $fsIndex => $fileScanner) {
            $fsClasses = $fileScanner->getClassNames();
            foreach ($fsClasses as $fsClassName) {
                $this->classToFileScanner[$fsClassName] = $fsIndex;
            }
        }
    }

    /**
     * Export
     *
     * @todo implement method
     */
    public static function export()
    {
        // @todo
    }

    /**
     * __ToString
     *
     * @todo implement method
     */
    public function __toString()
    {
        // @todo
    }
}
