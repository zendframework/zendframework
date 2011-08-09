<?php

namespace Zend\Http\Header;

class RetryAfter extends Header
{

    public function getName()
    {
        return 'Retry-After';
    }

}
