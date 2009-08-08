<?php
class Zend_Amf_Parse_Resource_StreamContext
{
    public function parse($resource)
    {
        return stream_context_get_options($resource);
    }
}
