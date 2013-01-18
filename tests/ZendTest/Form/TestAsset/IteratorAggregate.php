<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\TestAsset;

use Traversable;
use IteratorAggregate as IteratorAggregateInterface;

class IteratorAggregate implements IteratorAggregateInterface
{
    protected $iterator;

    public function __construct(Traversable $iterator)
    {
        $this->iterator = $iterator;
    }

    public function getIterator()
    {
        return $this->iterator;
    }
}
