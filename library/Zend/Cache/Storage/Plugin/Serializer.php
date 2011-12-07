<?php

namespace Zend\Cache\Storage\Plugin;

use Zend\Cache\Storage\Plugin,
    Zend\Cache\Storage\Capabilities,
    Zend\Cache\Storage\Event,
    Zend\Cache\Storage\PostEvent,
    Zend\EventManager\EventCollection,
    Zend\Serializer\Serializer as SerializerFactory,
    Zend\Serializer\Adapter as SerializerAdapter;

class Serializer implements Plugin
{

    protected $serializer;
    protected $capabilities = array();
    protected $handles = array();

    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    public function setOptions($options)
    {
        foreach ($options as $name => $value) {
            $m = 'set' . $name;
            $this->$m($value);
        }
    }

    public function getOptions()
    {
        return array(
            'serializer' => $this->getSerializer(),
        );
    }

    public function setSerializer(SerializerAdapter $serializer)
    {
        $this->serializer = $serializer;
        return $this;
    }

    public function getSerializer()
    {
        if (!$this->serializer) {
            return SerializerFactory::getDefaultAdapter();
        }
        return $this->serializer;
    }

    public function attach(EventCollection $eventCollection)
    {
        $index = \spl_object_hash($eventCollection);
        if (isset($this->handles[$index])) {
            throw new LogicException('Plugin already attached');
        }

        $handles = array();
        $this->handles[$index] = & $handles;

        // read
        $handles[] = $eventCollection->attach('getItem.post',  array($this, 'onReadItemPost'));
        $handles[] = $eventCollection->attach('getItems.post', array($this, 'onReadItemsPost'));

        // fetch / fetchAll
        $handles[] = $eventCollection->attach('fetch.post', array($this, 'onFetchPost'));
        $handles[] = $eventCollection->attach('fetchAll.post', array($this, 'onFetchAllPost'));

        // write
        $handles[] = $eventCollection->attach('setItem.pre',  array($this, 'onWriteItemPre'));
        $handles[] = $eventCollection->attach('setItems.pre', array($this, 'onWriteItemsPre'));

        $handles[] = $eventCollection->attach('addItem.pre',  array($this, 'onWriteItemPre'));
        $handles[] = $eventCollection->attach('addItems.pre', array($this, 'onWriteItemsPre'));

        $handles[] = $eventCollection->attach('replaceItem.pre',  array($this, 'onWriteItemPre'));
        $handles[] = $eventCollection->attach('replaceItems.pre', array($this, 'onWriteItemsPre'));

        $handles[] = $eventCollection->attach('checkAndSetItem.pre', array($this, 'onWriteItemPre'));

        // increment / decrement item(s)
        $handles[] = $eventCollection->attach('incrementItem.pre', array($this, 'onIncrementItemPre'));
        $handles[] = $eventCollection->attach('incrementItems.pre', array($this, 'onIncrementItemsPre'));

        $handles[] = $eventCollection->attach('decrementItem.pre', array($this, 'onDecrementItemPre'));
        $handles[] = $eventCollection->attach('decrementItems.pre', array($this, 'onDecrementItemsPre'));

        // overwrite capabilities
        $handles[] = $eventCollection->attach('getCapabilities.post',  array($this, 'onGetCapabilitiesPost'));

        return $this;
    }

    public function detach(EventCollection $eventCollection)
    {
        $index = \spl_object_hash($eventCollection);
        if (!isset($this->handles[$index])) {
            throw new LogicException('Plugin not attached');
        }

        // detach all handles of this index
        foreach ($this->handles[$index] as $handle) {
            $eventCollection->detach($handle);
        }

        // remove all detached handles
        unset($this->handles[$index]);

        return $this;
    }

    public function onReadItemPost(PostEvent $event)
    {
        $serializer = $this->getSerializer();
        $result = $event->getResult();
        $result = $serializer->unserialize($result);
        $event->setResult($result);
    }

    public function onReadItemsPost(PostEvent $event)
    {
        $serializer = $this->getSerializer();
        $result     = $event->getResult();
        foreach ($result as &$value) {
            $value = $serializer->unserialize($value);
        }
        $event->setResult($result);
    }

    public function onFetchPost(PostEvent $event)
    {
        $item = $event->getResult();
        if (isset($item['value'])) {
            $item['value'] = $this->getSerializer()->unserialize($item['value']);
        }
        $event->setResult($item);
    }

    public function onFetchAllPost(PostEvent $event)
    {
        $serializer = $this->getSerializer();
        $result     = $event->getResult();
        foreach ($result as &$item) {
            if (isset($item['value'])) {
                $item['value'] = $serializer->unserialize($item['value']);
            }
        }
        $event->setResult($result);
    }

    public function onWriteItemPre(Event $event)
    {
        $serializer = $this->getSerializer();
        $params     = $event->getParams();
        $params['value'] = $serializer->serialize($params['value']);
    }

    public function onWriteItemsPre(Event $event)
    {
        $serializer = $this->getSerializer();
        $params     = $event->getParams();
        foreach ($params['keyValuePairs'] as &$value) {
            $value = $serializer->serialize($value);
        }
    }

    public function onIncrementItemPre(Event $event)
    {
        $event->stopPropagation(true);

        $cache  = $event->getTarget();
        $params = $event->getParams();
        $token  = null;
        $oldValue = $cache->getItem($params['key'], array('token' => &$token) + $params['options']);
        return $cache->checkAndSetItem($token, $oldValue + $params['value'], $params['key'], $params['options']);
    }

    public function onIncrementItemsPre(Event $event)
    {
        $event->stopPropagation(true);

        $cache  = $event->getTarget();
        $params = $event->getParams();
        $keyValuePairs = $cache->getItems(array_keys($params['keyValuePairs']), $params['options']);
        foreach ($params['keyValuePairs'] as $key => &$value) {
            if (isset($keyValuePairs[$key])) {
                $keyValuePairs[$key]+= $value;
            } else {
                $keyValuePairs[$key] = $value;
            }
        }
        return $cache->setItems($keyValuePairs, $params['options']);
    }

    public function onDecrementItemPre(Event $event)
    {
        $event->stopPropagation(true);

        $cache  = $event->getTarget();
        $params = $event->getParams();
        $token  = null;
        $oldValue = $cache->getItem($params['key'], array('token' => &$token) + $params['options']);
        return $cache->checkAndSetItem($token, $oldValue - $params['value'], $params['key'], $params['options']);
    }

    public function onDecrementItemsPre(Event $event)
    {
        $event->stopPropagation(true);

        $cache  = $event->getTarget();
        $params = $event->getParams();
        $keyValuePairs = $cache->getItems(array_keys($params['keyValuePairs']), $params['options']);
        foreach ($params['keyValuePairs'] as $key => &$value) {
            if (isset($keyValuePairs[$key])) {
                $keyValuePairs[$key]-= $value;
            } else {
                $keyValuePairs[$key] = -$value;
            }
        }
        return $cache->setItems($keyValuePairs, $params['options']);
    }

    public function onGetCapabilitiesPost(PostEvent $event)
    {
        $baseCapabilities = $event->getResult();
        $index = \spl_object_hash($baseCapabilities);

        if (!isset($this->capabilities[$index])) {
            $this->capabilities[$index] = new Capabilities(
                new \stdClass(), // marker
                array('supportedDatatypes' => array(
                    'NULL'     => true,
                    'boolean'  => true,
                    'integer'  => true,
                    'double'   => true,
                    'string'   => true,
                    'array'    => true,
                    'object'   => 'object',
                    'resource' => false,
                )),
                $baseCapabilities
            );
        }

        $event->setResult($this->capabilities[$index]);
    }

}
