<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace Zend\Http\Header;

/**
 * Abstract Accept Header
 *
 * @category   Zend
 * @package    Zend\Http\Header
 * @see        http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
 */
abstract class AbstractAccept implements HeaderInterface
{

    /**
     *
     * @var array
     */
    protected $mediaRanges = array();

    protected $regexAddType;

    /**
     * Determines if since last mutation the stack was sorted
     * @var bool
     */
    protected $sorted = false;


    /**
     *
     * @param string (optional) $headerLine
     */
    public function __construct($headerLine = null)
    {
        if (!$headerLine) {
            return;
        }

        $fieldName = $this->getFieldName();
        $pos = strlen($fieldName)+2;
        if (substr($headerLine, 0, $pos) == $fieldName . ': ') {
            $headerLine = substr($headerLine, $pos);
        }

        foreach($this->getMediaRangesFromHeaderLine($headerLine) as $value) {
            $this->addMediaRangeToQueue($value);
        }
    }

    /**
     * Factory method: parse Accept header string
     *
     * @param  string $headerLine
     * @return Accept
     */
    public static function fromString($headerLine)
    {
        return new static($headerLine);
    }

    /**
     * Parse the media ranges represented by a header line
     *
     * @param string  $headerLine
     * @throws Exception\InvalidArgumentException If header is invalid
     * @return array
     */
    public function getMediaRangesFromHeaderLine($headerLine)
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

            $out[] = $this->getAcceptParamsFromMediaRangeString($value);
        }

        return $out;
    }

    /**
     * Parse the accept params belonging to a media range
     *
     * @param string $mediaType
     * @return StdClass
     */
    protected function getAcceptParamsFromMediaRangeString($mediaType)
    {
        $raw = $subtypeWhole = $type = $mediaType;
        if ($pos = strpos($mediaType, ';')) {
            $type = substr($mediaType, 0, $pos);
        }

        $params = $this->parseMediaRanges($mediaType);

        if ($pos = strpos($mediaType, ';')) {
            $mediaType = trim(substr($mediaType, 0, $pos));
        }

        $format = '*';
        $subtype = '*';

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
	 * Parse the keys contained in the header line
	 *
     * @param string mediaType
     */
    protected function parseMediaRanges ($mediaType)
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
    public function getFieldValue($values = null)
    {
        if (!$values) {
            return $this->getFieldValue($this->mediaRanges);
        }

        $strings = array();
        foreach ($values as $value) {
            $params = $value->params;
            array_walk($params, array($this, 'assembleAcceptParam'));
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
    protected function assembleAcceptParam(&$value, $key)
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

        $assembledString = $this->getFieldValue(
                                array((object)array('typeString' => $type, 'params' => $params))
                            );

        $value = $this->getAcceptParamsFromMediaRangeString($assembledString);
        $this->addMediaRangeToQueue($value);
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

    /**
     * Match a media string against this header
     *
     * @param array|string $matchAgainst
     * @return array|boolean The matched value or false
     */
    public function match($matchAgainst)
    {
        if (is_string($matchAgainst)) {
            $matchAgainst = $this->getMediaRangesFromHeaderLine($matchAgainst);
        }

        foreach ($this->getPrioritized() as $left) {
            foreach ($matchAgainst as $right) {
                if($right->type == '*' || $left->type == '*') {
                    if ($res = $this->matchAcceptParams($left, $right)) {
                        return $res;
                    }
                }

                if ($left->type == $right->type) {
                    if ((($left->subtype == $right->subtype ||
                            ($right->subtype == '*' || $left->subtype == '*')) &&
                            ($left->format == $right->format ||
                                    $right->format == '*' || $left->format == '*')))
                    {
                        if ($res = $this->matchAcceptParams($left, $right)) {
                            return $res;
                        }
                    }
                }

            }
        }

        return false;
    }

    /**
     * Return a match where all parameters in argument #1 match those in argument #2
     *
     * @param array $match1
     * @param array $match2
     * @return boolean|array
     */
    protected function matchAcceptParams($match1, $match2)
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
     * Add a key/value combination to the internal queue
     *
     * @param unknown_type $value
     * @return number
     */
    protected function addMediaRangeToQueue($value)
    {
        $this->mediaRanges[] = $value;
        $this->sorted = false;
    }

    /**
     * Sort the internal media ranges
     *
     * @See rfc2616 sect 14.1
     * Media ranges can be overridden by more specific media ranges or
     * specific media types. If more than one media range applies to a given
     * type, the most specific reference has precedence. For example,
     *
     * Accept: text/*, text/html, text/html;level=1, * /*
     *
     * have the following precedence:
     *
     * 1) text/html;level=1
     * 2) text/html
     * 3) text/*
     * 4) * /*
     *
     * @return number
     */
    protected function sortMediaRanges()
    {
        $sort = function($a, $b) // If A has higher prio than B, return -1.
        {
            if ($a->priority > $b->priority) {
                return -1;
            } elseif ($a->priority < $b->priority) {
                return 1;
            }

            // Asterisks
            $values = array('type', 'subtype','format');
            foreach($values as $value) {
                if($a->$value == '*' && $b->$value != '*') {
                    return 1;
                } elseif($b->$value == '*' && $a->$value != '*') {
                    return -1;
                }
            }

            if($a->type == 'application' && $b->type != 'application') {
                return -1;
            } elseif($b->type == 'application' && $a->type != 'application') {
                return 1;
            }

            //@todo count number of dots in case of type==application in subtype

            // So far they're still the same. Longest stringlength may be more specific
            if(strlen($a->raw) == strlen($b->raw)) return 0;
            return (strlen($a->raw) > strlen($b->raw)) ? -1 : 1;
        };

        usort($this->mediaRanges, $sort);
        $this->sorted = true;
    }

    /**
     * @return array with all the keys, values and parameters this header represents:
     */
    public function getPrioritized()
    {
        if (!$this->sorted) {
            $this->sortMediaRanges();
        }

        return $this->mediaRanges;
    }

}
