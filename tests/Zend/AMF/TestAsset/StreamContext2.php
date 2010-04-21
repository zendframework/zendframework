<?php

namespace ZendTest\AMF\TestAsset;

class StreamContext2
{
    public function parse($resource)
    {
        return stream_context_get_options($resource);
    }
}

