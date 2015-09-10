<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mail\Header;

use Zend\Mail\Headers;
use Zend\Mime\Mime;

class ContentType implements UnstructuredInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * Header encoding
     *
     * @var string
     */
    protected $encoding = 'ASCII';

    /**
     * @var array
     */
    protected $parameters = array();

    public static function fromString($headerLine)
    {
        list($name, $value) = GenericHeader::splitHeaderLine($headerLine);
        $value = HeaderWrap::mimeDecodeValue($value);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'content-type') {
            throw new Exception\InvalidArgumentException('Invalid header line for Content-Type string');
        }

        $value  = str_replace(Headers::FOLDING, ' ', $value);
        $values = preg_split('#\s*;\s*#', $value);

        $type   = array_shift($values);
        $header = new static();
        $header->setType($type);

        // Remove empty values
        $values = array_filter($values);

        foreach ($values as $keyValuePair) {
            list($key, $value) = explode('=', $keyValuePair, 2);
            $value = trim($value, "'\" \t\n\r\0\x0B");
            $header->addParameter($key, $value);
        }

        return $header;
    }

    public function getFieldName()
    {
        return 'Content-Type';
    }

    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        $prepared = $this->type;
        if (empty($this->parameters)) {
            return $prepared;
        }

        $values = array($prepared);
        foreach ($this->parameters as $attribute => $value) {
            if (HeaderInterface::FORMAT_ENCODED === $format && !Mime::isPrintable($value)) {
                $this->encoding = 'UTF-8';
                $value = HeaderWrap::wrap($value, $this);
                $this->encoding = 'ASCII';
            }

            $values[] = sprintf('%s="%s"', $attribute, $value);
        }

        return implode(';' . Headers::FOLDING, $values);
    }

    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    public function getEncoding()
    {
        return $this->encoding;
    }

    public function toString()
    {
        return 'Content-Type: ' . $this->getFieldValue(HeaderInterface::FORMAT_ENCODED);
    }

    /**
     * Set the content type
     *
     * @param  string $type
     * @throws Exception\InvalidArgumentException
     * @return ContentType
     */
    public function setType($type)
    {
        if (!preg_match('/^[a-z-]+\/[a-z0-9.+-]+$/i', $type)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a value in the format "type/subtype"; received "%s"',
                __METHOD__,
                (string) $type
            ));
        }
        $this->type = $type;
        return $this;
    }

    /**
     * Retrieve the content type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add a parameter pair
     *
     * @param  string $name
     * @param  string $value
     * @return ContentType
     * @throws Exception\InvalidArgumentException for parameter names that do not follow RFC 2822
     * @throws Exception\InvalidArgumentException for parameter values that do not follow RFC 2822
     */
    public function addParameter($name, $value)
    {
        $name  = strtolower($name);
        $value = (string) $value;

        if (! HeaderValue::isValid($name)) {
            throw new Exception\InvalidArgumentException('Invalid content-type parameter name detected');
        }
        if (! HeaderWrap::canBeEncoded($value)) {
            throw new Exception\InvalidArgumentException(
                'Parameter value must be composed of printable US-ASCII or UTF-8 characters.'
            );
        }

        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * Get all parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get a parameter by name
     *
     * @param  string $name
     * @return null|string
     */
    public function getParameter($name)
    {
        $name = strtolower($name);
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }
        return;
    }

    /**
     * Remove a named parameter
     *
     * @param  string $name
     * @return bool
     */
    public function removeParameter($name)
    {
        $name = strtolower($name);
        if (isset($this->parameters[$name])) {
            unset($this->parameters[$name]);
            return true;
        }
        return false;
    }
}
