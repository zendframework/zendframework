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
 * @package    Zend_LDAP
 * @subpackage Node
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\LDAP\Node;

use Zend\LDAP\Node;

/**
 * Zend_LDAP_Node_Collection provides a collecion of nodes.
 *
 * @uses       \Zend\LDAP\Collection
 * @uses       \Zend\LDAP\Node
 * @category   Zend
 * @package    Zend_LDAP
 * @subpackage Node
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Collection extends \Zend\LDAP\Collection
{
    /**
     * Creates the data structure for the given entry data
     *
     * @param  array $data
     * @return \Zend\LDAP\Node
     */
    protected function _createEntry(array $data)
    {
        $node = Node::fromArray($data, true);
        $node->attachLDAP($this->_iterator->getLDAP());
        return $node;
    }

    /**
     * Return the child key (DN).
     * Implements Iterator and RecursiveIterator
     *
     * @return string
     */
    public function key()
    {
        return $this->_iterator->key();
    }
}
