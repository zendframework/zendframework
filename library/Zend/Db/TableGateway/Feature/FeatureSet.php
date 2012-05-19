<?php

namespace Zend\Db\TableGateway\Feature;

use Zend\Db\TableGateway\AbstractTableGateway;

class FeatureSet
{
    const APPLY_HALT = 'halt';

    protected $tableGateway = null;

    /**
     * @var AbstractFeature[]
     */
    protected $features = array();

    /**
     * @var array
     */
    protected $magicSpecifications = array();

    public function __construct(array $features = array())
    {
        if ($features) {
            $this->addFeatures($features);
        }
    }

    public function setTableGateway(AbstractTableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        foreach ($this->features as $feature) {
            $feature->setTableGateway($this->tableGateway);
        }
        return $this;
    }

    public function getFeatureByClassName($featureClassName)
    {
        $feature = false;
        foreach ($this->features as $potentialFeature) {
            if ($potentialFeature instanceof $featureClassName) {
                $feature = $potentialFeature;
                break;
            }
        }
        return $feature;
    }

    public function addFeatures(array $features)
    {
        foreach ($features as $feature) {
            $this->addFeature($feature);
        }
        return $this;
    }

    public function addFeature(AbstractFeature $feature)
    {
        $this->features[] = $feature;
        $feature->setTableGateway($feature);
        return $this;
    }

    public function apply($method, $args)
    {
        foreach ($this->features as $feature) {
            if (method_exists($feature, $method)) {
                $return = call_user_func_array(array($feature, $method), $args);
                if ($return === self::APPLY_HALT) {
                    break;
                }
            }
        }
    }

    public function canCallMagicGet($property)
    {

    }

    public function callMagicGet($property)
    {

    }

    public function canCallMagicSet($property)
    {

    }

    public function callMagicSet($property, $value)
    {

    }

    public function canCallMagicCall($method)
    {

    }

    public function callMagicCall($method, $arguments)
    {

    }
}
