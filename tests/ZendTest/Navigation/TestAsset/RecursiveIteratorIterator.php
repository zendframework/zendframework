<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Navigation\TestAsset;

class RecursiveIteratorIterator extends \RecursiveIteratorIterator
{
    /**
     *
     * @var \ArrayAccess|array
     */
    public $logger = array();

    public function beginIteration()
    {
        $this->logger[] = 'beginIteration';
    }

    public function endIteration()
    {
        $this->logger[] = 'endIteration';
    }

    public function beginChildren()
    {
        $this->logger[] = 'beginChildren';
    }

    public function endChildren()
    {
        $this->logger[] = 'endChildren';
    }

    public function current()
    {
        $this->logger[] = parent::current()->getLabel();
    }
}
