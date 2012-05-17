<?php

namespace Zend\Db\Adapter\Driver\Feature;

interface DriverFeatureInterface
{
    public function setupDefaultFeatures();
    public function addFeature($name, $feature);
    public function getFeature($name);
}