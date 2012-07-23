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

use Mongo;
use Zend\Log\Exception\InvalidArgumentException;
use Zend\Log\Exception\RuntimeException;
use Zend\Log\Formatter\FormatterInterface;

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
     * @param Mongo|array $mongo
     * @param string $database
     * @param string $collection
     * @param array  $saveOptions
     * @return Zend\Log\Writer\MongoDB
     */
    public function __construct($mongo, $database, $collection, array $saveOptions = array())
    {
        if (is_array($mongo)) {
            if (isset($mongo[3]) && is_array($mongo[3])) {
                $saveOptions = $mongo[3];
            }
            if (isset($mongo[2])) {
                $collection = $mongo[2];
            }
            if (isset($mongo[1])) {
                $database = $mongo[1];
            }
            $mongo = $mongo[0];
        }
        if (!($mongo instanceof Mongo)) {
            throw new Exception\InvalidArgumentException(
                'Parameter of type %s is invalid; must be Mongo',
                (is_object($mongo) ? get_class($mongo) : gettype($mongo)) 
            );
        }
        $this->mongoCollection = $mongo->selectCollection($database, $collection);
        $this->saveOptions = $saveOptions;
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

        $this->mongoCollection->save($event, $this->saveOptions);
    }
}
