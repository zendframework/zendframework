<?php

namespace Zend\Mail\Header;

class Subject implements HeaderDescription, UnstructuredHeader
{
    /**
     * @var string
     */
    protected $subject = '';

    /**
     * Factory from header line
     * 
     * @param  string $headerLine 
     * @return Subject
     */
    public static function fromString($headerLine)
    {
        list($name, $value) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'subject') {
            throw new Exception\InvalidArgumentException('Invalid header line for Subject string');
        }

        $header = new static();
        $header->setSubject($value);
        
        return $header;
    }

    /**
     * Get the header name
     * 
     * @return string
     */
    public function getFieldName()
    {
        return 'Subject';
    }

    /**
     * Get the header value
     * 
     * @return string
     */
    public function getFieldValue()
    {
        return HeaderWrap::wrap($this->subject, $this);
    }

    /**
     * Set the value of the header
     * 
     * @param  string $subject 
     * @return Subject
     */
    public function setSubject($subject)
    {
        $this->subject = (string) $subject;
        return $this;
    }

    /**
     * String representation of header
     * 
     * @return string
     */
    public function toString()
    {
        return 'Subject: ' . $this->getFieldValue();
    }
}
