<?php

namespace ZendTest\Amf\TestAsset;

class StreamContext3
{
    protected function parse($resource)
    {
        return stream_context_get_options($resource);
    }
}
