<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\TestAsset;

/**
 * Dummy derived TestOptions used to test Stdlib\Options
 */
class TestOptionsDerived extends TestOptions
{
    private $derivedPrivate;

    protected $derivedProtected;

    protected $derivedPublic;

    /**
     * Needed to test accessibility of getters / setters within deriving classes
     */
    private function setDerivedPrivate($derivedPrivate)
    {
        $this->derivedPrivate = $derivedPrivate;
    }

    /**
     * Needed to test accessibility of getters / setters within deriving classes
     */
    private function getDerivedPrivate()
    {
        return $this->derivedPrivate;
    }

    /**
     * Needed to test accessibility of getters / setters within deriving classes
     */
    protected function setDerivedProtected($derivedProtected)
    {
        $this->derivedProtected = $derivedProtected;
    }


    /**
     * Needed to test accessibility of getters / setters within deriving classes
     */
    protected function getDerivedProtected()
    {
        return $this->derivedProtected;
    }

    /**
     * Needed to test accessibility of getters / setters within deriving classes
     */
    public function setDerivedPublic($derivedPublic)
    {
        $this->derivedPublic = $derivedPublic;
    }


    /**
     * Needed to test accessibility of getters / setters within deriving classes
     */
    public function getDerivedPublic()
    {
        return $this->derivedPublic;
    }
}
