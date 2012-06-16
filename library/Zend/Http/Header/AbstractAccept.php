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
    protected $priorityQueue;

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

        $fieldName = $acceptHeader->getFieldName();
        $pos = strlen($fieldName)+2;
        if (substr($headerLine, 0, $pos) == $fieldName . ': ') {
            $headerLine = substr($headerLine, $pos);
        }

        $acceptHeader->values = $acceptHeader->getPayloadsFromHeaderLine($headerLine);

        return $acceptHeader;
    }

    public function getPayloadsFromHeaderLine($headerLine)
    {
        // process multiple accept values, they may be between quotes
        if (!preg_match_all('/(?:[^,"]|"(?:[^\\\"]|\\\.)*")+/', $headerLine, $values)
                || !isset($values[0])
        ) {
            throw new Exception\InvalidArgumentException(
                    'Invalid header line for ' . $this->getFieldName() . ' header string'
            );
        }

        $out = array();
        foreach ($values[0] as $value) {
            $value = trim($value);

            $out[] = $this->getPayloadValuesFromString($value);
        }

        return $out;
    }

    protected function getPayloadValuesFromString($mediaType)
    {
        $raw = $mediaType;
        if ($pos = strpos($mediaType, '/')) {
            $type = trim(substr($mediaType, 0, $pos));
        } else {
            $type = trim(substr($mediaType, 0));
        }

        $params = $this->parseParams($mediaType);

        if ($pos = strpos($mediaType, ';')) {
            $mediaType = trim(substr($mediaType, 0, $pos));
        }

        if ($pos = strpos($mediaType, '/')) {
            $subtypeWhole = $format = $subtype = trim(substr($mediaType, strpos($mediaType, '/')+1));
        } else {
            $subtypeWhole = '';
            $format = '*';
            $subtype = '*';
        }

        $pos = strpos($subtype, '+');
        if (false !== $pos) {
            $format = trim(substr($subtype, $pos+1));
            $subtype = trim(substr($subtype, 0, $pos));
        }

        return (object) array(
                            'typeString' => trim($mediaType),
                            'type'    => $type,
                            'subtype' => $subtype,
                            'subtypeRaw' => $subtypeWhole,
                            'format'  => $format,
                            'priority' => isset($params['q']) ? $params['q'] : 1,
                            'params' => $params,
                            'raw' => trim($raw)
        );
    }

	/**
     * @param mediaType
     */
    private function parseParams ($mediaType)
    {
        $params = array();
        if (($pos = strpos($mediaType,';'))) {
            preg_match_all('/(?:[^;"]|"(?:[^\\\"]|\\\.)*")+/', $mediaType, $paramsStrings);

            if (isset($paramsStrings[0])) {
                array_shift($paramsStrings[0]);
                $paramsStrings = $paramsStrings[0];
            } else {
                $paramsStrings = array();
            }

            foreach($paramsStrings as $param) {
                $explode = explode('=', $param, 2);

                $value = trim($explode[1]);
                if ($value[0] == '"' && substr($value, -1) == '"') {
                    $value = substr(substr($value,1), 0, -1);
                }

                $params[trim($explode[0])] = stripslashes($value);
            }
        }

        return $params;
    }


    /**
     * Get field value
     *
     * @return string
     */
    public function getFieldValue()
    {
        return $this->getFieldValueInternal($this->values);
    }

    protected function getFieldValueInternal(array $values)
    {
        $strings = array();
        foreach ($values as $value) {
            $params = $value->params;
            array_walk($params, array($this, 'assembleFieldValueParam'));
            $strings[] = implode(';', array($value->typeString) + $params);
        }

        return implode(', ', $strings);
    }

    /*
     * Assemble and escape the field value parameters based on RFC 2616 secion 2.1
     *
     * @todo someone should review this thoroughly
     * @param string value
     * @param string $key
     * @return void
     */
    protected function assembleFieldValueParam(&$value, $key)
    {
        $separators = array('(', ')', '<', '>', '@', ',', ';', ':',
                            '/', '[', ']', '?', '=', '{', '}',  ' ',  "\t");

        $escaped = preg_replace_callback('/[[:cntrl:]"\\\\]/', // escape cntrl, ", \
                                         function($v) { return '\\' . $v[0]; },
                                         $value
                    );

        if ($escaped == $value && ! array_intersect(str_split($value), $separators)) {
            $value = $key.'='.$value;
        } else {
            $value = $key.'="'.$escaped.'"';
        }

        return $value;
    }

    /**
     * Add a type, with the given priority
     *
     * @param  string $type
     * @param  int|float $priority
     * @param  int $level
     * @return Accept
     */
    protected function addType($type, $priority = 1, array $params = array())
    {
        if (!preg_match($this->regexAddType, $type)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a valid type; received "%s"',
                __METHOD__,
                (string) $type
            ));
        }

        if (!is_int($priority) && !is_float($priority) && !is_numeric($priority)
            || $priority > 1 || $priority < 0
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a numeric priority; received %s',
                __METHOD__,
                (string) $priority
            ));
        }

        if ($priority != 1) {
            $params = array('q' => sprintf('%01.1f', $priority)) + $params;
        }

        $assembledString = $this->getFieldValueInternal(
                                array((object)array('typeString' => $type, 'params' => $params))
                            );

        $this->values[] = $this->getPayloadValuesFromString($assembledString);
        return $this;
    }



    /**
     * Does the header have the requested type?
     *
     * @param  string $type
     * @return bool
     */
    protected function hasType($matchAgainst)
    {
        return (bool) $this->match($matchAgainst);
    }

    public function match($matchAgainst)
    {
        if (is_string($matchAgainst)) {
            $matchAgainst = $this->getPayloadsFromHeaderLine($matchAgainst);
        }

        foreach ($matchAgainst as $left) {
            foreach ($this->values as $right) {
                if($right->type == '*' || $left->type == '*') {
                    if ($res = $this->_matchParams($left, $right)) {
                        return $res;
                    }
                }

                if ($left->type == $right->type) {
                    if ((($left->subtype == $right->subtype ||
                            ($right->subtype == '*' || $left->subtype == '*')) &&
                            ($left->format == $right->format ||
                                    $right->format == '*' || $left->format == '*')))
                    {
                        if ($res = $this->_matchParams($right, $left)) {
                            return $res;
                        }
                    }
                }

            }
        }

        return false;
    }

    protected function _matchParams($match1, $match2)
    {
        foreach($match2->params as $key => $value) {
            if (isset($match1->params[$key])) {
                if (strpos($value, '-')) {
                    $values = explode('-', $value, 2);
                    if($values[0] > $match1->params[$key] ||
                            $values[1] < $match1->params[$key])
                    {
                        return false;
                    }
                } elseif (strpos($value, '|')) {
                    $options = explode('|', $value);
                    $good = false;
                    foreach($options as $option) {
                        if($option == $match1->params[$key]) {
                            $good = true;
                            break;
                        }
                    }

                    if (!$good) {
                        return false;
                    }
                } elseif($match1->params[$key] != $value) {
                    return false;
                }
            }

        }

        return $match1;
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
            // Do not include priority 0 in list. see RFC2616 section 3.9
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
//                     $queue->insert($data, (float) $data['priority'] * 1000 + $level);
        }
        $this->priorityQueue = $queue;
    }
}
