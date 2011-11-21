<?php

namespace Zend\Mail;

interface AddressDescription
{
    public function getEmail();
    public function getName();
    public function toString();
}
