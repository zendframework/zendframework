<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Interface.php 17687 2009-08-20 12:55:34Z thomas $
 */

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Paginator_SerializableLimitIterator extends LimitIterator implements Serializable
{

    /**
     * Offset to first element
     *
     * @var int
     */
    private $_offset;

    /**
     * Maximum number of elements to show or -1 for all
     *
     * @var int
     */
    private $_count;

    /**
     * Construct a Zend_Paginator_SerializableLimitIterator
     *
     * @param Iterator $it Iterator to limit (must be serializable by un-/serialize)
     * @param int $offset Offset to first element
     * @param int $count Maximum number of elements to show or -1 for all
     * @see LimitIterator::__construct
     */
    public function __construct (Iterator $it, $offset=0, $count=-1)
    {
        parent::__construct($it, $offset, $count);
        $this->_offset = $offset;
        $this->_count = $count;
    }

    /**
     * @return string representation of the instance
     */
    public function serialize()
    {
        return serialize(array(
            'it'     => $this->getInnerIterator(),
            'offset' => $this->_offset,
            'count'  => $this->_count,
            'pos'    => $this->getPosition(),
        ));
    }

    /**
     * @param string $data representation of the instance
     */
    public function unserialize($data)
    {
        $dataArr = unserialize($data);
        $this->__construct($dataArr['it'], $dataArr['offset'], $dataArr['count']);
        $this->seek($dataArr['pos']+$dataArr['offset']);
    }

}
