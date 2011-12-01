<?php

namespace Zend\Cache\Storage\Adapter;

use Zend\Cache\Storage\Capabilities,
    Zend\Cache\Utils,
    Zend\Cache\Exception\RuntimeException,
    Zend\Cache\Exception\InvalidArgumentException,
    Zend\Cache\Exception\ItemNotFoundException;

class Memory extends AbstractAdapter
{

    /**
     * Data Array
     *
     * Format:
     * array(
     *     <NAMESPACE> => array(
     *         <KEY> => array(
     *             0 => <VALUE>
     *             1 => <MICROTIME>
     *             2 => <TAGS>
     *         )
     *     )
     * )
     *
     * @var array
     */
    protected $_data = array();

    /* reading */

    public function getItem($key = null, array $options = array())
    {
        if (!$this->getReadable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $key  = $this->_key($key);
        $args = new \ArrayObject(array(
            'key'     => & $key,
            'options' => & $options
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $ns = $options['namespace'];
        $exist = isset($this->_data[$ns][$key]);
        if ($exist) {
            if ($options['ttl'] && microtime(true) >= ($this->_data[$ns][$key][1] + $options['ttl']) ) {
                $exist = false;
            }
        }

        if (!$exist) {
            if (!$options['ignore_missing_items']) {
                throw new ItemNotFoundException("Key '{$key}' not found on namespace '{$ns}'");
            }
            $result = false;
            $args['_RESULT_'] = false;
        } else {
            $result = $this->_data[$ns][$key][0];
            if (array_key_exists('token', $options)) {
                $options['token'] = $this->_data[$ns][$key][0];
            }
        }

        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    public function getItems(array $keys, array $options = array())
    {
        if (!$this->getReadable()) {
            return array();
        }

        $this->_normalizeOptions($options);
        $args = new \ArrayObject(array(
            'keys'    => & $keys,
            'options' => & $options
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $ns = $options['namespace'];
        if (!isset($this->_data[$ns])) {
            $result = array();
        } else {
            $data = &$this->_data[$ns];

            $keyValuePairs = array();
            foreach ($keys as $key) {
                if (isset($data[$key])) {
                    if (!$options['ttl'] || microtime(true) < ($this->_data[$ns][$key][1] + $options['ttl']) ) {
                        $keyValuePairs[$key] = $data[$key][0];
                    }
                }
            }

            $result = $keyValuePairs;
        }

        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    public function hasItem($key = null, array $options = array())
    {
        if (!$this->getReadable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $key  = $this->_key($key);
        $args = new \ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $result = $this->_exists($key, $options);

        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    public function getMetadata($key = null, array $options = array())
    {
        if (!$this->getReadable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $key  = $this->_key($key);
        $args = new \ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        if (!$this->_exists($key, $options)) {
            if (!$options['ignore_missing_items']) {
                throw new ItemNotFoundException(
                    "Key '{$key}' not found on namespace '{$options['namespace']}'"
                );
            }
            $result = false;
        } else {
            $ns = $options['namespace'];
            $result = array(
                'mtime' => $this->_data[$ns][$key][1],
                'tags'  => $this->_data[$ns][$key][2],
            );
        }

        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    /* writing */

    public function setItem($value, $key = null, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $key  = $this->_key($key);
        $args = new \ArrayObject(array(
            'key'     => & $key,
            'value'   => & $value,
            'options' => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $ns = $options['namespace'];
        $this->_data[$ns][$key] = array($value, microtime(true), $options['tags']);

        $result = true;
        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    public function setItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $args = new \ArrayObject(array(
            'keyValuePairs' => & $keyValuePairs,
            'options'       => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $ns = $options['namespace'];
        if (!isset($this->_data[$ns])) {
            $this->_data[$ns] = array();
        }

        $data = & $this->_data[$ns];
        foreach ($keyValuePairs as $key => $value) {
            $data[$key] = array($value, microtime(true), $options['tags']);
        }

        $result = true;
        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    public function addItem($value, $key = null, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $key  = $this->_key($key);
        $args = new \ArrayObject(array(
            'key'     => & $key,
            'value'   => & $value,
            'options' => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $ns = $options['namespace'];
        if (isset($this->_data[$ns][$key])) {
            throw new RuntimeException("Key '{$key}' already exists within namespace '$ns'");
        }
        $this->_data[$ns][$key] = array($value, microtime(true), $options['tags']);

        $result = true;
        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    public function addItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $args = new \ArrayObject(array(
            'keyValuePairs' => & $keyValuePairs,
            'options'       => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $ns = $options['namespace'];
        if (!isset($this->_data[$ns])) {
            $this->_data[$ns] = array();
        }

        $data = & $this->_data[$ns];
        foreach ($keyValuePairs as $key => $value) {
            if (isset($data[$key])) {
                throw new RuntimeException("Key '{$key}' already exists within namespace '$ns'");
            }
            $data[$key] = array($value, microtime(true), $options['tags']);
        }

        $result = true;
        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    public function replaceItem($value, $key = null, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $key  = $this->_key($key);
        $args = new \ArrayObject(array(
            'key'     => & $key,
            'value'   => & $value,
            'options' => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $ns = $options['namespace'];
        if (!isset($this->_data[$ns][$key])) {
            throw new ItemNotFoundException("Key '{$key}' doen't exists within namespace '$ns'");
        }
        $this->_data[$ns][$key] = array($value, microtime(true), $options['tags']);

        $result = true;
        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    public function replaceItems(array $keyValuePairs, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $args = \ArrayObject(array(
            'keyValuePairs' => & $keyValuePairs,
            'options'       => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $ns = $options['namespace'];
        if (!isset($this->_data[$ns])) {
            throw new ItemNotFoundException("Namespace '$ns' doesn't exist");
        }

        $data = & $this->_data[$ns];
        foreach ($keyValuePairs as $key => $value) {
            if (!isset($data[$key])) {
                throw new ItemNotFoundException(
                    "Key '{$key}' doen't exists within namespace '$ns'"
                );
            }
            $data[$key] = array($value, microtime(true), $options['tags']);
        }

        $result = true;
        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    public function touchItem($key = null, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $key  = $this->_key($key);
        $args = new \ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $ns = $options['namespace'];
        if (isset($this->_data[$ns][$key])) {
            // update mtime
            $this->_data[$ns][$key][1] = microtime(true);
        } else {
            if (!$options['ignore_missing_items']) {
                throw new ItemNotFoundException(
                    "Key '{$key}' not found within namespace '{$ns}'"
                );
            }

            // add an empty item
            $this->_data[$ns][$key] = array('', microtime(true), null);
        }

        $result = true;
        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    public function removeItem($key = null, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $key = $this->_key($key);
        $args = new \ArrayObject(array(
            'key'     => & $key,
            'options' => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $ns = $options['namespace'];
        if (isset($this->_data[$ns][$key])) {
            unset($this->_data[$ns][$key]);

            // remove empty namespace
            if (!$this->_data[$ns]) {
                unset($this->_data[$ns]);
            }

        } else {
            if (!$options['ignore_missing_items']) {
                throw new ItemNotFoundException("Key '{$key}' not found on namespace '{$ns}'");
            }
        }

        $result = true;
        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    public function removeItems(array $keys, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $args = new \ArrayObject(array(
            'keys'    => & $keys,
            'options' => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $ns = $options['namespace'];
        if ($options['ignore_missing_items'] === false) {
            if (!isset($this->_data[$ns])) {
                throw new ItemNotFoundException("Namespace '{$ns}' is empty");
            }

            $data = &$this->_data[$ns];

            $missingItems = null;
            foreach ($keys as $key) {
                if (isset($data[$key])) {
                    unset($data[$key]);
                } else {
                    $missingItems[] = $key;
                }
            }

            if ($missingItems) {
                throw new ItemNotFoundException(
                    "Keys '" . implode("','", $missingItems) . "' not found on namespace '{$ns}'"
                );
            }
        } elseif (isset($this->_data[$ns])) {
            $data = & $this->_data[$ns];
            foreach ($keys as $key) {
                unset($data[$key]);
            }

            // remove empty namespace
            if (!$data) {
                unset($this->_data[$ns]);
            }
        }

        $result = true;
        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    public function incrementItem($value, $key = null, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $key   = $this->_key($key);
        $value = (int)$value;
        $args  = new \ArrayObject(array(
            'key'     => & $key,
            'value'   => & $value,
            'options' => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $ns   = $options['namespace'];
        $data = & $this->_data[$ns];
        if (isset($data[$key])) {
            $data[$key][0]+= $value;
            $data[$key][1] = microtime(true);
            $result = $data[$key][0];
        } else {
            if (!$options['ignore_missing_items']) {
                throw new ItemNotFoundException(
                    "Key '{$key}' not found within namespace '{$ns}'"
                );
            }

            // add a new item
            $data[$key] = array($value, microtime(true), null);
            $result = $value;
        }

        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    public function decrementItem($value, $key = null, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $value = (int)$value;
        $key   = $this->_key($key);
        $args  = new \ArrayObject(array(
            'key'     => & $key,
            'value'   => & $value,
            'options' => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $ns   = $options['namespace'];
        $data = & $this->_data[$ns];
        if (isset($data[$key])) {
            $data[$key][0]-= $value;
            $data[$key][1] = microtime(true);
            $result = $data[$key][0];
        } else {
            if (!$options['ignore_missing_items']) {
                throw new ItemNotFoundException(
                    "Key '{$key}' not found within namespace '{$ns}'"
                );
            }

            // add a new item
            $data[$key] = array(-$value, microtime(true), null);
            $result = -$value;
        }

        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    /* non-blocking */

    public function find($mode = self::MATCH_ACTIVE, array $options=array())
    {
        if (!$this->getReadable()) {
            return false;
        }

        if ($this->_stmtActive) {
            throw new RuntimeException('Statement already in use');
        }

        $this->_normalizeOptions($options);
        $this->_normalizeMatchingMode($mode, self::MATCH_ACTIVE, $options);
        $args = new \ArrayObject(array(
            'mode'    => & $mode,
            'options' => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $tags = & $options['tags'];
        $emptyTags = $keys = array();
        foreach ($this->_data[ $options['namespace'] ] as $key => &$item) {

            // compare expired / active
            if (($mode & self::MATCH_ALL) != self::MATCH_ALL) {

                // if MATCH_EXPIRED -> filter active items
                if (($mode & self::MATCH_EXPIRED) == self::MATCH_EXPIRED) {
                    if ($this->_exists($key, $options)) {
                        continue;
                    }

                // if MATCH_ACTIVE -> filter expired items
                } else {
                    if (!$this->_exists($key, $options)) {
                        continue;
                    }
                }
            }

            // compare tags
            if ($tags !== null) {
                $tagsStored = isset($item[2]) ? $item[2] : $emptyTags;

                if ( ($mode & self::MATCH_TAGS_OR) == self::MATCH_TAGS_OR ) {
                    $matched = (count(array_diff($tags, $tagsStored)) != count($tags));
                } elseif ( ($mode & self::MATCH_TAGS_AND) == self::MATCH_TAGS_AND ) {
                    $matched = (count(array_diff($tags, $tagsStored)) == 0);
                }

                // negate
                if ( ($mode & self::MATCH_TAGS_NEGATE) == self::MATCH_TAGS_NEGATE ) {
                    $matched = !$matched;
                }

                if (!$matched) {
                    continue;
                }
            }

            $keys[] = $key;
        }

        // don't check expiry on fetch
        $options['ttl'] = 0;

        $this->_stmtKeys    = $keys;
        $this->_stmtOptions = $options;
        $this->_stmtActive  = true;

        $result = true;
        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    public function fetch()
    {
        if (!$this->_stmtActive) {
            return false;
        }

        $args = new \ArrayObject();
        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $options = & $this->_stmtOptions;

        // get the next valid item
        do {
            $key = array_shift($this->_stmtKeys);
            if ($key === null) {
                break;
            }
            if (!$this->_exists($key, $options)) {
                continue;
            }
            $ref = & $this->_data[ $options['namespace'] ][$key];
            break;
        } while (true);

        // get item data
        if ($key) {
            $item = array();
            foreach ($options['select'] as $select) {
                if ($select == 'key') {
                    $item['key'] = $key;
                } elseif ($select == 'value') {
                    $item['value'] = $ref[0];
                } elseif ($select == 'mtime') {
                    $item['mtime'] = $ref[1];
                } elseif ($select == 'tags') {
                    $item['tags'] = $ref[2];
                } else {
                    $item[$select] = null;
                }
            }

            $result = $item;

        } else {
            // free statement after last item
            $this->_stmtActive  = false;
            $this->_stmtKeys    = null;
            $this->_stmtOptions = null;

            $result = false;
        }

        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    /* cleaning */

    public function clear($mode = self::MATCH_EXPIRED, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $this->_normalizeMatchingMode($mode, self::MATCH_EXPIRED, $options);

        $args = new \ArrayObject(array(
            'mode'    => & $mode,
            'options' => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        if (!$options['tags'] && ($mode & self::MATCH_ALL) == self::MATCH_ALL) {
            $this->_data = array();
        } else {
            foreach ($this->_data as &$data) {
                $this->_clearNamespacedDataArray($data, $mode, $options);
            }
        }

        $result = true;
        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    public function clearByNamespace($mode = self::MATCH_EXPIRED, array $options = array())
    {
        if (!$this->getWritable()) {
            return false;
        }

        $this->_normalizeOptions($options);
        $this->_normalizeMatchingMode($mode, self::MATCH_EXPIRED, $options);

        $args = new \ArrayObject(array(
            'mode'    => & $mode,
            'options' => & $options,
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        if (isset($this->_data[ $options['namespace'] ])) {
            if (!$options['tags'] && ($mode & self::MATCH_ALL) == self::MATCH_ALL) {
                unset($this->_data[ $options['namespace'] ]);
            } else {
                $this->_clearNamespacedDataArray($this->_data[ $options['namespace'] ], $mode, $options);
            }
        }

        $result = true;
        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    protected function _clearNamespacedDataArray(array &$data, $mode, &$options)
    {
        $tags = &$options['tags'];
        $time = microtime(true);
        $ttl  = $options['ttl'];

        $emptyTags = $keys = array();
        foreach ($data as $key => &$item) {

            // compare expired / active
            if (($mode & self::MATCH_ALL) != self::MATCH_ALL) {

                // if MATCH_EXPIRED mode selected don't match active items
                if (($mode & self::MATCH_EXPIRED) == self::MATCH_EXPIRED) {
                    if ($ttl == 0 || $time <= ($item[1]+$ttl) ) {
                        continue;
                    }

                // if MATCH_ACTIVE mode selected don't match expired items
                } elseif ($ttl > 0 && $time >= ($item[1]+$ttl)) {
                    continue;
                }
            }

            // compare tags
            if ($tags !== null) {
                $tagsStored = isset($item[2]) ? $item[2] : $emptyTags;

                if ( ($mode & self::MATCH_TAGS_OR) == self::MATCH_TAGS_OR ) {
                    $matched = (count(array_diff($tags, $tagsStored)) != count($tags));
                } elseif ( ($mode & self::MATCH_TAGS_AND) == self::MATCH_TAGS_AND ) {
                    $matched = (count(array_diff($tags, $tagsStored)) == 0);
                }

                // negate
                if ( ($mode & self::MATCH_TAGS_NEGATE) == self::MATCH_TAGS_NEGATE ) {
                    $matched = !$matched;
                }

                if (!$matched) {
                    continue;
                }
            }

            unset($data[$key]);
        }
    }

    /* status */

    public function getCapabilities()
    {
        $args = new \ArrayObject();

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        if ($this->_capabilities === null) {
            $this->_capabilityMarker = new \stdClass();
                $this->_capabilities = new Capabilities(
                $this->_capabilityMarker,
                array(
                    'supportedDatatypes' => array(
                        'NULL'     => true,
                        'boolean'  => true,
                        'integer'  => true,
                        'double'   => true,
                        'string'   => true,
                        'array'    => true,
                        'object'   => true,
                        'resource' => true
                    ),
                    'supportedMetadata' => array(
                        'mtime', 'tags'
                    ),
                    'maxTtl'             => PHP_INT_MAX,
                    'staticTtl'          => false,
                    'ttlPrecision'       => 0.05,
                    'expiredRead'        => true,
                    'maxKeyLength'       => 0,
                    'namespaceIsPrefix'  => false,
                    'namespaceSeparator' => '',
                    'iterable'           => true,
                    'clearAllNamespaces' => true,
                    'clearByNamespace'   => true,
                )
            );
        }

        $postEvent = $this->triggerPost(__FUNCTION__, $args, $this->_capabilities);
        return $postEvent->getResult();
    }

    public function getCapacity(array $options = array())
    {
        $args = new \ArrayObject(array(
            'options' => & $options
        ));

        $eventRs = $this->triggerPre(__FUNCTION__, $args);
        if ($eventRs->stopped()) {
            return $eventRs->last();
        }

        $result = Utils::getPhpMemoryCapacity();

        $postEvent = $this->triggerPost(__FUNCTION__, $args, $result);
        return $postEvent->getResult();
    }

    /* internal */

    protected function _exists($key, array &$options)
    {
        $ns = $options['namespace'];

        if (!isset($this->_data[$ns][$key])) {
            return false;
        }

        // check if expired
        if ($options['ttl'] && microtime(true) >= ($this->_data[$ns][$key][1] + $options['ttl']) ) {
            return false;
        }

        return true;
    }

    public function _info($key, array &$options = array())
    {
        if (!$this->_exists($key, $options)) {
            if (!$options['ignore_missing_items']) {
                throw new ItemNotFoundException(
                    "Key '{$key}' not found on namespace '{$options['namespace']}'"
                );

            }
            return false;
        }

        $ns    = $options['namespace'];
        $mtime = $this->_data[$ns][$key][1];

        return array(
            'mtime' => $mtime,
            'ttl'   => $mtime - microtime(true) + $options['ttl'],
            'tags'  => $this->_data[$ns][$key][2]
        );
    }

}
