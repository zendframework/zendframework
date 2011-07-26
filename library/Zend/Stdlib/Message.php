<?php

namespace Zend\Stdlib;

use Traversable;

class Message implements MessageDescription
{
    protected $metadata = array();
    protected $content;

    /**
     * Set message metadata 
     *
     * Non-destructive setting of message metadata; always adds to the metadata, never overwrites 
     * the entire metadata container.
     * 
     * @param  string|int|array|Traversable $spec 
     * @param  mixed $value 
     * @return Message
     */
    public function setMetadata($spec, $value = null)
    {
        if (is_scalar($spec)) {
            $this->metadata[$spec] = $value;
            return $this;
        }
        if (!is_array($spec) && !$spec instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected a string, array, or Traversable argument in first position; received "%s"',
                (is_object($spec) ? get_class($spec) : gettype($spec))
            ));
        }
        foreach ($spec as $key => $value) {
            $this->metadata[$key] = $value;
        }
        return $this;
    }

    /**
     * Retrieve all metadata or a single metadatum as specified by key
     * 
     * @param  null|string|int $key 
     * @param  null|mixed $default
     * @return mixed
     */
    public function getMetadata($key = null, $default = null)
    {
        if (null === $key) {
            return $this->metadata;
        }

        if (!is_scalar($key)) {
            throw new Exception\InvalidArgumentException('Non-scalar argument provided for key');
        }

        if (array_key_exists($key, $this->metadata)) {
            return $this->metadata[$key];
        }

        return $default;
    }

    /**
     * Set message content
     * 
     * @param  mixed $value 
     * @return Message
     */
    public function setContent($value)
    {
        $this->content = $value;
        return $this;
    }

    /**
     * Get message content
     * 
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
    
    public function __toString()
    {
        $request = '';
        foreach ($this->getMetadata() as $key => $value) {
            $request .= sprintf(
                "%s: %s\r\n",
                (string) $key,
                (string) $value
            );
        }
        $request .= "\r\n" . $this->getContent();

    }

    public function fromString($string)
    {
        throw new Exception\DomainException('Unimplemented: ' . __METHOD__);
    }
}
