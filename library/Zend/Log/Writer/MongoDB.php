<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace Zend\Log\Writer;

use DateTime;
use Mongo;
use MongoDate;
use Traversable;
use Zend\Log\Exception\InvalidArgumentException;
use Zend\Log\Exception\RuntimeException;
use Zend\Log\Formatter\FormatterInterface;
use Zend\Stdlib\ArrayUtils;

/**
 * MongoDB log writer.
 *
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 */
class MongoDB extends AbstractWriter
{
    /**
     * MongoCollection instance
     *
     * @var MongoCollection
     */
    protected $mongoCollection;

    /**
     * Options used for MongoCollection::save()
     *
     * @var array
     */
    protected $saveOptions;

    /**
     * Constructor
     *
     * @param Mongo|array|Traversable $mongo
     * @param string|MongoDB $database
     * @param string $collection
     * @param array $saveOptions
     */
    public function __construct($mongo, $database, $collection, array $saveOptions = array())
    {
        if ($mongo instanceof Traversable) {
            // Configuration may be multi-dimensional due to save options
            $mongo = ArrayUtils::iteratorToArray($mongo);
        }
        if (is_array($mongo)) {
            $saveOptions = isset($mongo['save_options']) ? $mongo['save_options'] : null;
            $collection  = isset($mongo['collection']) ? $mongo['collection'] : null;
            if (null === $collection) {
                throw new Exception\InvalidArgumentException(
                    'The collection parameter cannot be empty'
                );
            }
            $database = isset($mongo['database']) ? $mongo['database'] : null;
            if (null === $database) {
                throw new Exception\InvalidArgumentException(
                    'The database parameter cannot be empty'
                );
            }
            $mongo = isset($mongo['mongo']) ? $mongo['mongo'] : null;
        }

        if (!($mongo instanceof Mongo)) {
            throw new Exception\InvalidArgumentException(
                'Parameter of type %s is invalid; must be Mongo',
                (is_object($mongo) ? get_class($mongo) : gettype($mongo))
            );
        }

        $this->mongoCollection = $mongo->selectCollection($database, $collection);
        $this->saveOptions     = $saveOptions;
    }

    /**
     * This writer does not support formatting.
     *
     * @param Zend\Log\Formatter\FormatterInterface $formatter
     * @return void
     * @throws Zend\Log\Exception\InvalidArgumentException
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        throw new InvalidArgumentException(get_class() . ' does not support formatting');
    }

    /**
     * Write a message to the log.
     *
     * @param array $event Event data
     * @return void
     * @throws Zend\Log\Exception\RuntimeException
     */
    protected function doWrite(array $event)
    {
        if (null === $this->mongoCollection) {
            throw new RuntimeException('MongoCollection must be defined');
        }

        if (isset($event['timestamp']) && $event['timestamp'] instanceof DateTime) {
            $event['timestamp'] = new MongoDate($event['timestamp']->getTimestamp());
        }

        $this->mongoCollection->save($event, $this->saveOptions);
    }
}
