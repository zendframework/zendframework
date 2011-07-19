<?php

namespace Zend\Stdlib;

class Response extends Message implements ResponseDescription
{
    public function send()
    {
        echo $this->getContent();
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
