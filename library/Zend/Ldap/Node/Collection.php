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
 * @package    Zend_Ldap
 * @subpackage Node
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Ldap\Node;

use Zend\Ldap;

/**
 * Zend\Ldap\Node\Collection provides a collection of nodes.
 *
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage Node
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Collection extends Ldap\Collection
{
    /**
     * Creates the data structure for the given entry data
     *
     * @param  array $data
     * @return \Zend\Ldap\Node
     */
    protected function createEntry(array $data)
    {
        $node = Ldap\Node::fromArray($data, true);
        $node->attachLDAP($this->iterator->getLDAP());
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
        return $this->iterator->key();
    }
}
