<?php

namespace Zend\Messenger;

interface Delivery
{
    public function notify($topic, $argv = null);
    public function notifyUntil($callback, $topic, $argv = null);
    public function filter($topic, $value);
    public function attach($topic, $context, $handler = null);
    public function detach(Handler $handle);
    public function getTopics();
    public function getHandlers($topic);
    public function clearHandlers($topic);
}
