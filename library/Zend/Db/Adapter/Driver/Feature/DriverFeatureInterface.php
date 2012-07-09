<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Package
 */

namespace Zend\Db\Adapter\Driver\Feature;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
interface DriverFeatureInterface
{
    public function setupDefaultFeatures();
    public function addFeature($name, $feature);
    public function getFeature($name);
}
