<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend\Http\Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
namespace Zend\Http\Header;

use Zend\Stdlib\PriorityQueue;

/**
 * Abstract Accept Header
 *
 * @category   Zend
 * @package    Zend\Http\Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
 */
abstract class AbstractAccept implements HeaderInterface
{

    protected $values = array();
    protected $prioritizedValues = array();
    protected $priorityQueue;
    protected $types = array();

    protected $regexAddType; 
    
    /**
     * Factory method: parse Accept header string
     * 
     * @param  string $headerLine 
     * @return Accept
     */
    public static function fromString($headerLine)
    {
        $acceptHeader = new static();

        list($name, $values) = explode(': ', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== strtolower($acceptHeader->getFieldName())) {
            throw new Exception\InvalidArgumentException('Invalid header line for ' . $acceptHeader->getFieldName() . ' header string');
        }

        // process multiple accept values
        $acceptHeader->values = explode(',', $values);

        foreach ($acceptHeader->values as $index => $value) {
            $value = trim($value);
            $acceptHeader->values[$index] = $value;

            $payload = array(
                'type' => strtolower($value),
                'priority'   => 1,
            );
            if (strstr($value, ';')) {
                list($type, $priority) = explode(';', $value, 2);
                $payload['type'] = strtolower(trim($type));

                // parse priority
                $priority = explode(';', trim($priority));

                $finalPriority = 1;
                foreach ($priority as $p) {
                    list($type, $value) = explode('=', trim($p), 2);
                    if ($type == 'q') {
                        $payload['priority'] = $value;
                    } elseif ($type == 'level') {
                        $payload['level'] = $value;
                    }
                }
            }

            if (!isset($acceptHeader->types[$payload['type']])) {
                $acceptHeader->types[$payload['type']] = true;
            }

            $acceptHeader->prioritizedValues[] = $payload;
        }

        return $acceptHeader;
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
     * Add a type, with the given priority
     * 
     * @param  string $type 
     * @param  int|float $priority 
     * @param  int $level 
     * @return Accept
     */
    protected function addType($type, $priority = 1, $level = null)
    {
        if (!preg_match($this->regexAddType, $type)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a valid type; received "%s"',
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

        if (!empty($level) && (!is_numeric($level) || $level < 0)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an integer level greater than 0; received %s',
                __METHOD__,
                $level
            ));
        }
        
        $this->types[$type] = true;

        if (!empty($level)) {
            $this->prioritizedValues[] = array(
                'type' => $type,
                'priority'   => $priority,
                'level'      => (integer) $level
            );
        } else {
            $this->prioritizedValues[] = array(
                'type' => $type,
                'priority'   => $priority
            );
        }
        
        $value = $type;
        if (!empty($level)) {
            $value .= sprintf(';level=%d', $level);
        }
        if ($priority < 1) {
            $value .= sprintf(';q=%01.1f', $priority);
        }
        $this->values[] = $value;

        return $this;
    }

    /**
     * Does the header have the requested type?
     * 
     * @param  string $type 
     * @return bool
     */
    protected function hasType($type)
    {
        $type = strtolower($type);

        // Exact match
        if (isset($this->types[$type])) {
            return true;
        }

        // Check for media type
        if (false !== strstr($type, '/')) {      
            // Parent type wildcard matching
            $parent = substr($type, 0, strpos($type, '/'));
            if (isset($this->types[$parent . '/*'])) {
                return true;
            }

            // Wildcard matching
            if (isset($this->types['*/*'])) {
                return true;
            }
        } else {
            if (isset($this->types['*'])) {
                return true;
            }
        }
        // No match
        return false;
    }

    /**
     * Get a prioritized list of types
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
            $suffix = '';
            $level = 0;
            if (!empty($data['level'])) {
                $level = $data['level'];
                $suffix = ";level=$level";
            }
            $queue->insert($data['type'].$suffix, (float) $data['priority'] * 1000 + $level);
        }
        $this->priorityQueue = $queue;
    }
}
