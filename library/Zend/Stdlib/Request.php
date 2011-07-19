<?php

namespace Zend\Stdlib;

class Request extends Message implements RequestDescription
{
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
