<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\TestAsset;

use Zend\Stdlib\AbstractOptions;

/**
 * Dummy TestOptions used to test Stdlib\Options
 */
class TestOptions extends AbstractOptions
{
    protected $testField;

    private $parentPrivate;

    protected $parentProtected;

    protected $parentPublic;

    public function setTestField($value)
    {
        $this->testField = $value;
    }

    public function getTestField()
    {
        return $this->testField;
    }

    /**
     * Needed to test accessibility of getters / setters within deriving classes
     */
    private function setParentPrivate($parentPrivate)
    {
        $this->parentPrivate = $parentPrivate;
    }

    /**
     * Needed to test accessibility of getters / setters within deriving classes
     */
    private function getParentPrivate()
    {
        return $this->parentPrivate;
    }

    /**
     * Needed to test accessibility of getters / setters within deriving classes
     */
    protected function setParentProtected($parentProtected)
    {
        $this->parentProtected = $parentProtected;
    }


    /**
     * Needed to test accessibility of getters / setters within deriving classes
     */
    protected function getParentProtected()
    {
        return $this->parentProtected;
    }

    /**
     * Needed to test accessibility of getters / setters within deriving classes
     */
    public function setParentPublic($parentPublic)
    {
        $this->parentPublic = $parentPublic;
    }


    /**
     * Needed to test accessibility of getters / setters within deriving classes
     */
    public function getParentPublic()
    {
        return $this->parentPublic;
    }
}
