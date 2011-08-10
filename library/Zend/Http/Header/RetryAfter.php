<?php

namespace Zend\Http\Header;

class RetryAfter extends GenericHeader
{

    public function getName()
    {
        return 'Retry-After';
    }

}
