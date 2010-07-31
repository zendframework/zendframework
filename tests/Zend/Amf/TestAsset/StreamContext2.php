<?php

namespace ZendTest\Amf\TestAsset;

class StreamContext2
{
    public function parse($resource)
    {
        return stream_context_get_options($resource);
    }
}

