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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Ldap_TestCase
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestCase.php';
/**
 * @see Zend_Ldap_Node
 */
require_once 'Zend/Ldap/Node.php';

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Node
 */
class Zend_Ldap_Node_AttributeIterationTest extends Zend_Ldap_TestCase
{
    public function testSimpleIteration()
    {
        $node=$this->_createTestNode();
        $i=0;
        $data=array();
        foreach ($node->getAttributes() as $k => $v) {
            $this->assertNotNull($k);
            $this->assertNotNull($v);
            $this->assertEquals($node->$k, $v);
            $data[$k]=$v;
            $i++;
        }
        $this->assertEquals(5, $i);
        $this->assertEquals($i, count($node));
        $this->assertEquals(array(
            'boolean'     => array(true, false),
            'cn'          => array('name'),
            'empty'       => array(),
            'host'        => array('a', 'b', 'c'),
            'objectclass' => array('account', 'top')), $data);
    }
}
