<?php

namespace Zend\Http\Header;

use Zend\Stdlib\PriorityQueue;

/**
 * @todo Implement level lookups
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
 */
class Accept implements HeaderDescription
{

    protected $values = array();
    protected $prioritizedValues = array();
    protected $priorityQueue;
    protected $mediaTypes = array();

    /**
     * Factory method: parse Accept header string
     * 
     * @param  string $headerLine 
     * @return Accept
     */
    public static function fromString($headerLine)
    {
        $acceptHeader = new static();

        list($name, $values) = preg_split('#: #', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'accept') {
            throw new Exception\InvalidArgumentException('Invalid header line for accept header string: "' . $name . '"');
        }

        // process multiple accept values
        // @todo level processing
        $acceptHeader->values = explode(',', $values);

        foreach ($acceptHeader->values as $index => $value) {
            $value = trim($value);
            $acceptHeader->values[$index] = $value;

            $payload = array(
                'media_type' => strtolower($value),
                'priority'   => 1,
            );
            if (strstr($value, ';')) {
                list($type, $priority) = explode(';', $value, 2);
                $payload['media_type'] = strtolower(trim($type));

                // parse priority
                $priority = explode(';', trim($priority));

                $finalPriority = 1;
                foreach ($priority as $p) {
                    list($type, $value) = explode('=', trim($p), 2);
                    if ($type != 'q') {
                        // Not going to worry about "level" for now
                        continue;
                    }
                    $finalPriority = $value;
                }
                $payload['priority'] = $finalPriority;
            }

            if (!isset($acceptHeader->mediaTypes[$payload['media_type']])) {
                $acceptHeader->mediaTypes[$payload['media_type']] = true;
            }

            $acceptHeader->prioritizedValues[] = $payload;
        }

        return $acceptHeader;
    }

    /**
     * Get field name
     * 
     * @return string
     */
    public function getFieldName()
    {
        return 'Accept';
    }

    /**
     * Get field value
     * 
     * @return string
     */
    public function getFieldValue()
    {
        $strings = array();
        foreach ($this->values as $value) {
            $strings[] = implode('; ', (array) $value);
        }
        return implode(',', $strings);
    }

    /**
     * Cast to string
     * 
     * @return string
     */
    public function toString()
    {
        return 'Accept: ' . $this->getFieldValue();
    }

    /**
     * Add a media type, with the given priority
     * 
     * @param  string $type 
     * @param  int|float $priority 
     * @param  int $level Unused currently
     * @return Accept
     */
    public function addMediaType($type, $priority = 1, $level = null)
    {
        if (!preg_match('#^([a-zA-Z+-]+|\*)/(\*|[a-zA-Z0-9+-]+)$#', $type)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a valid media type; received "%s"',
                __METHOD__,
                (string) $type
            ));
        }

        if (!is_int($priority) && !is_float($priority) && !is_numeric($priority)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a numeric priority; received %s',
                __METHOD__,
                (string) $priority
            ));
        }

        if ($priority > 1 || $priority < 0) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a priority between 0 and 1; received %01.1f',
                __METHOD__,
                (float) $priority
            ));
        }

        $this->mediaTypes[$type] = true;

        $this->prioritizedValues[] = array(
            'media_type' => $type,
            'priority'   => $priority,
        );

        $value = $type;
        if ($priority < 1) {
            $value .= sprintf(';q=%01.1f', $priority);
        }
        $this->values[] = $value;

        return $this;
    }

    /**
     * Does the header have the requested media type?
     * 
     * @param  string $type 
     * @return bool
     */
    public function hasMediaType($type)
    {
        $type = strtolower($type);

        // Exact match
        if (isset($this->mediaTypes[$type])) {
            return true;
        }

        // No "/" -- not a media type
        if (false === strstr($type, '/')) {
            return false;
        }

        // Parent type wildcard matching
        $parent = substr($type, 0, strpos($type, '/'));
        if (isset($this->mediaTypes[$parent . '/*'])) {
            return true;
        }

        // Wildcard matching
        if (isset($this->mediaTypes['*/*'])) {
            return true;
        }

        // No match
        return false;
    }

    /**
     * Get a prioritized list of media types
     * 
     * @return PriorityQueue
     */
    public function getPrioritized()
    {
        if (!$this->priorityQueue) {
            $this->createPriorityQueue();
        }

        return $this->priorityQueue;
    }

    /**
     * Create the priority queue
     * 
     * @return void
     */
    protected function createPriorityQueue()
    {
        $queue = new PriorityQueue();
        foreach ($this->prioritizedValues as $data) {
            // Do not include priority 0 in list
            if ($data['priority'] == 0) {
                continue;
            }

            // Hack to ensure priorities are correct; was not treating 
            // fractional values correctly
            $queue->insert($data['media_type'], (float) $data['priority'] * 10);
        }
        $this->priorityQueue = $queue;
    }
}
