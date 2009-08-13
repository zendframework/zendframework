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
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Feed
 */
require_once 'Zend/Feed.php';

/**
 * @see Zend_Feed_Atom
 */
require_once 'Zend/Feed/Atom.php';

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Feed
 */
class Zend_Feed_AtomEntryOnlyTest extends PHPUnit_Framework_TestCase
{

    public function testEntryOnly()
    {
        $feed = new Zend_Feed_Atom(null, file_get_contents(dirname(__FILE__) . '/_files/TestAtomFeedEntryOnly.xml'));

        $this->assertEquals(1, $feed->count(), 'The entry-only feed should report one entry.');

        foreach ($feed as $entry);
        $this->assertEquals('Zend_Feed_Entry_Atom', get_class($entry),
                            'The single entry should be an instance of Zend_Feed_Entry_Atom');

        $this->assertEquals('1', $entry->id(), 'The single entry should have id 1');
        $this->assertEquals('Bug', $entry->title(), 'The entry\'s title should be "Bug"');
    }

}
