<?php

namespace ZendTest\Mail\TestAsset;

class StringSerializableObject
{
    public function __construct($message)
    {
        $this->message = $message;
    }

    public function __toString()
    {
        return $this->message;
    }
}
