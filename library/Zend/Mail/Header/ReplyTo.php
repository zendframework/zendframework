<?php

namespace Zend\Mail\Header;

class ReplyTo extends AbstractAddressList
{
    protected $fieldName = 'Reply-To';
    protected static $type = 'reply-to';
}
