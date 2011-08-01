<?php

namespace Zend\Http;

class Header
{
    /**
     * @var string
     */
    protected $type = null;

    /**
     * @var string
     */
    protected $value = null;

    /**
     * @var array
     */
    protected $arrayValue = array();
    
    /**
     * @var bool
     */
    protected $replaceFlag = null;

    /**
     * Constructor
     * 
     * @param  string $header 
     * @param  string|array $value 
     * @param  bool $replace 
     * @return void
     */
    public function __construct($header, $value = null, $replace = false)
    {
        if (strpos($header,':') !== false) {
            // construct the header from a raw string
            $this->fromString($header);
        } else {
            if (is_array($header) || $header instanceof ArrayObject) {
                $type    = $header['type'] ?: false;
                $value   = $header['value'] ?: '';
                $replace = (bool) ($header['replace'] ?: false);
                $header  = $type;
            }
            $this->setType($header);
            $this->setValue($value);
        }
        $this->replace($replace);
    }

    /**
     * Set header type
     * 
     * @param  string $type 
     * @return Header
     */
    public function setType($type)
    {
        if (!is_string($type) || empty($type)) {
            throw new Exception\InvalidArgumentException('Header type must be a string');
        }

        // Pre-filter to normalize valid characters
        $type = $this->normalizeHeaderType((string) $type);

        // Validate what we have
        if (!preg_match('/^[a-z][a-z0-9-]*$/i', $type)) {
            throw new Exception\InvalidArgumentException('Header type must start with a letter, and consist of only letters, numbers, and dashes');
        }

        $this->type = $type;
        return $this;
    }

    /**
     * Set header value
     * 
     * @param  string|array $value 
     * @return Header
     */
    public function setValue($value, $separator = '; ')
    {
        if (is_array($value)) {
            $value = implode($separator, $value);
        }

        $value = (string) $value;

        if (empty($value) || preg_match('/^\s+$/', $value)) {
            $value = '';
        }

        $this->arrayValue = array();

        if (preg_match('/^accept/i', $this->type)) {
            $values = explode(',', $value);
            if (!empty($values[1])) {
                foreach ($values as $key) {
                    $key = trim($key);
                    $parts = explode(';', $key);
                    if (!empty($parts[1])) {
                        $num = count($parts);
                        for ($i = 1; $i < $num; $i++) {
                            $this->arrayValue[$parts[0]][]= trim($parts[$i]);
                        }
                    } else {
                        $this->arrayValue[$key]= null; 
                    }    
                }
            } 
        }

        $this->value = $value;
        return $this;
    }

    /**
     * Retrieve or set "replace" flag
     *
     * Used by the Headers class when sending headers.
     *
     * If a null flag is passed (or no argument passed), returns the value of 
     * the flag; otherwise, sets it.
     * 
     * @param  null|bool $flag 
     * @return Header|bool
     */
    public function replace($flag = null)
    {
        if (null === $flag) {
            return $this->replaceFlag;
        }

        $this->replaceFlag = (bool) $flag;
        return $this;
    }

    /**
     * Retrieve header type
     * 
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Retrieve header value
     * 
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Normalize the header string
     * 
     * @param  string $string 
     * @return string
     */
    protected function normalizeHeaderType($string)
    {
        $type = str_replace(array('_', '-'), ' ', $string);
        $type = ucwords($type);

        return str_replace(' ', '-', $type);
    }

    /**
     * Return true if the header has a specified value
     * 
     * @param string $value
     * @return boolean 
     */
    public function hasValue($value)
    {
        if (!empty($this->arrayValue)) {
            return array_key_exists($value, $this->arrayValue);
        } else {
            return ($value==$this->value);     
        }
    }

    /**
     * Get the quality factor of the value (q=)
     * 
     * @param string $value
     * @return float
     */
    public function getQualityFactor($value)
    {
        if ($this->hasValue($value)) {
            if (!empty($this->arrayValue)) {
                if (isset($this->arrayValue[$value])) {
                    foreach ($this->arrayValue[$value] as $val) {
                        if (preg_match('/q=(\d\.?\d?)/', $val, $matches)) {
                            return $matches[1];
                        }
                    }
                }
                return 1;
            }
        }
        return false;
    }

    /**
     * Get the level of a value (level=)
     * 
     * @param string $value
     * @return integer 
     */
    public function getLevel($value)
    {
        if ($this->hasValue($value)) {
            if (isset($this->arrayValue[$value])) {
                foreach ($this->arrayValue[$value] as $val) {
                    if (preg_match('/level=(\d+)/', $val, $matches)) {
                        return $matches[1];
                    }
                }
            }
        }    
        return false;
    }

    /**
     * Set the header from a raw string
     *
     * @param string $string
     * @return boolean
     */
    public function fromString($string)
    {
        if (!empty($string)) {

            $parts = explode(':', $string);

            if (!empty($parts[1])) {
                $this->setType(trim($parts[0]));
                $this->setValue(trim($parts[1]));
                return true;
            }

            throw new Exception\InvalidArgumentException('The header specified is not valid');
        }

        return false;
    }

    /**
     * Cast to string
     *
     * Returns in form of "TYPE: VALUE\r\n"
     *
     * @return string
     */
    public function __toString()
    {
        $type  = $this->getType();
        $value = $this->getValue();

        return $type . ': ' . $value . "\r\n";
    }

}
