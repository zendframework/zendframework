<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib\TestAsset;

/**
 * Test asset to verify that a composition of a class-methods and an array-serializable
 * hydrator produces the expected output
 */
class AggregateObject
{
    /**
     * @var array
     */
    public $arrayData  = array('president' => 'Zaphod');

    /**
     * @var string
     */
    public $maintainer = 'Marvin';

    /**
     * @return string
     */
    public function getMaintainer()
    {
        return $this->maintainer;
    }

    /**
     * @param string $maintainer
     */
    public function setMaintainer($maintainer)
    {
        $this->maintainer = $maintainer;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return $this->arrayData;
    }

    /**
     * @param array $data
     */
    public function exchangeArray(array $data)
    {
        $this->arrayData = $data;
    }
}
