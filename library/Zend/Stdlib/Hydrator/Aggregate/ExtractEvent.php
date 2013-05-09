<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\Aggregate;


use Zend\EventManager\Event;
use Zend\Stdlib\ArrayUtils;

class ExtractEvent extends Event
{
    const EVENT_EXTRACT = 'extract';

    /**
     * {@inheritDoc}
     */
    protected $name = self::EVENT_EXTRACT;

    /**
     * @var object
     */
    protected $extractionObject;

    /**
     * @var array
     */
    protected $extractedData = array();

    public function __construct($target, $extractionObject)
    {
        $this->target           = $target;
        $this->extractionObject = $extractionObject;
    }

    public function getExtractionObject()
    {
        return $this->extractionObject;
    }

    public function setExtractionObject($extractionObject)
    {
        $this->extractionObject = $extractionObject;
    }

    public function getExtractedData()
    {
        return $this->extractedData;
    }

    public function setExtractedData(array $extractedData)
    {
        $this->extractedData = $extractedData;
    }

    public function mergeExtractedData(array $additionalData)
    {
        $this->extractedData = array_merge($this->extractedData, $additionalData);
    }
}