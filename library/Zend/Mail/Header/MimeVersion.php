<?php

namespace Zend\Mail\Header;

class MimeVersion implements HeaderDescription
{
    /**
     * @var string Version string
     */
    protected $version = '1.0';

    public static function fromString($headerLine)
    {
        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'mime-version') {
            throw new Exception\InvalidArgumentException('Invalid header line for Mime-Version string');
        }

        // Check for address, and set if found
        $header = new static();
        if (preg_match('/^(?<version>\d+\.\d+)$/', $value, $matches)) {
            $header->version = $matches['version'];
        }
        
        return $header;
    }

    /**
     * Get the field name
     * 
     * @return string
     */
    public function getFieldName()
    {
        return 'Mime-Version';
    }

    /**
     * Get the field value (version string)
     * 
     * @return string
     */
    public function getFieldValue()
    {
        return $this->version;
    }

    /**
     * Serialize to string
     * 
     * @return string
     */
    public function toString()
    {
        return 'Mime-Version: ' . $this->getFieldValue();
    }
    
    /**
     * Set the version string used in this header
     * 
     * @param  string $version
     * @return MimeVersion
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Retrieve the version string for this header
     * 
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
}
