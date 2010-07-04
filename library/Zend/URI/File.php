<?php

namespace \Zend\URI;
use \Zend\URI\URI;

class File extends URI
{
    protected $_validSchemes = array('file');
    
    /**
     * Convert a UNIX file path to a valid file:// URL
     * 
     * @param  srting $path
     * @return \Zend\URI\File
     */
    static public function fromUnixPath($path)
    {
        $url = new self('file:');
        if (substr($path, 0, 1) == '/') {
            $url->setHost('');
        }
        $url->setPath($path);
        
        return $url;
    }
    
    /**
     * Convert a Windows file path to a valid file:// URL
     * 
     * @param  string $path
     * @return \Zend\URI\File
     */
    static public function fromWindowsPath($path)
    {
        $url = new self('file:');

        // Convert directory separators
        $path = str_replace(array('/', '\\'), array('%2F', '/'), $path);
        
        // Is this an absolute path?
        if (preg_match('|^([a-zA-Z]:)?/|', $path)) {
            $url->setHost('');
        }
        $url->setPath($path);
    }
}