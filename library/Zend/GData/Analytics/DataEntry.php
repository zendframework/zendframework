<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Analytics;

use Zend\GData;

/**
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Analytics
 */
class DataEntry extends GData\Entry
{
    /**
     * @var Extension\Dimension[]
     */
    protected $_dimensions = array();
    /**
     * @var Extension\Metric[]
     */
    protected $_metrics = array();

    /**
     * @param DOMElement $element
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(GData\Analytics::$namespaces);
        parent::__construct($element);
    }

    /**
     * @param DOMElement $child
     * @return void
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
            case $this->lookupNamespace('ga') . ':' . 'dimension';
                $dimension = new Extension\Dimension();
                $dimension->transferFromDOM($child);
                $this->_dimensions[] = $dimension;
                break;
            case $this->lookupNamespace('ga') . ':' . 'metric';
                $metric = new Extension\Metric();
                $metric->transferFromDOM($child);
                $this->_metrics[] = $metric;
                break;
            default:
                parent::takeChildFromDOM($child);
                break;
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getDimension($name)
    {
        foreach ($this->_dimensions as $dimension) {
            if ($dimension->getName() == $name) {
                return $dimension;
            }
        }
        return null;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getMetric($name)
    {
        foreach ($this->_metrics as $metric) {
            if ($metric->getName() == $name) {
                return $metric;
            }
        }
        return null;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getValue($name)
    {
        if (null !== ($metric = $this->getMetric($name))) {
            return $metric;
        }
        return $this->getDimension($name);
    }
}
