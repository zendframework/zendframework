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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Pdf;
use Zend\Pdf\InternalType;
use Zend\Pdf\Action;
use Zend\Pdf\Util;
use Zend\Pdf\ObjectFactory;
use Zend\Pdf;
use Zend\Pdf\Destination;

/** \Zend\Pdf\Action */


/** PHPUnit Test Case */


/**
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_PDF
 */
class ActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Stores the original set timezone
     * @var string
     */
    private $_originaltimezone;

    public function setUp()
    {
        $this->_originaltimezone = date_default_timezone_get();
        date_default_timezone_set('GMT');
    }

    /**
     * Teardown environment
     */
    public function tearDown()
    {
        date_default_timezone_set($this->_originaltimezone);
    }

    public function testLoad()
    {
        $dictionary = new InternalType\DictionaryObject();
        $dictionary->Type = new InternalType\NameObject('Action');
        $dictionary->S    = new InternalType\NameObject('GoTo');
        $dictionary->D    = new InternalType\StringObject('SomeNamedDestination');

        $action2Dictionary = new InternalType\DictionaryObject();
        $action2Dictionary->Type = new InternalType\NameObject('Action');
        $action2Dictionary->S    = new InternalType\NameObject('Thread');
        $action2Dictionary->D    = new InternalType\StringObject('NamedDestination 2');
        $action2Dictionary->Next = new InternalType\ArrayObject();

        $dictionary->Next = $action2Dictionary;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('GoTo');
        $leafAction->D    = new InternalType\StringObject('NamedDestination 3');
        $action2Dictionary->Next->items[] = $leafAction;


        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('GoToR');
        $action2Dictionary->Next->items[] = $leafAction;


        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('GoToE');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Launch');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Thread');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('URI');
        $leafAction->URI  = new InternalType\NameObject('http://some_host/');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Sound');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Movie');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Hide');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Named');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('SubmitForm');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('ResetForm');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('ImportData');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('JavaScript');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('SetOCGState');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Rendition');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Trans');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('GoTo3DView');
        $action2Dictionary->Next->items[] = $leafAction;

        $action = Action\AbstractAction::load($dictionary);

        $actionsCount = 0;
        $iterator = new \RecursiveIteratorIterator(new Util\RecursivelyIteratableObjectsContainer(array($action)),
                                                  \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $chainedAction) {
            $actionsCount++;
        }

        $this->assertEquals(20, $actionsCount);
    }

    public function testExtract()
    {
        $dictionary = new InternalType\DictionaryObject();
        $dictionary->Type = new InternalType\NameObject('Action');
        $dictionary->S    = new InternalType\NameObject('GoToR');
        $dictionary->D    = new InternalType\StringObject('SomeNamedDestination');

        $action2Dictionary = new InternalType\DictionaryObject();
        $action2Dictionary->Type = new InternalType\NameObject('Action');
        $action2Dictionary->S    = new InternalType\NameObject('Thread');
        $action2Dictionary->D    = new InternalType\StringObject('NamedDestination 2');
        $action2Dictionary->Next = new InternalType\ArrayObject();

        $dictionary->Next = $action2Dictionary;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('GoTo');
        $leafAction->D    = new InternalType\StringObject('NamedDestination 3');
        $action2Dictionary->Next->items[] = $leafAction;


        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('GoToR');
        $action2Dictionary->Next->items[] = $leafAction;


        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('GoToE');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Launch');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Thread');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('URI');
        $leafAction->URI  = new InternalType\NameObject('http://some_host/');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Sound');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Movie');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Hide');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Named');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('SubmitForm');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('ResetForm');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('ImportData');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('JavaScript');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('SetOCGState');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Rendition');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('Trans');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new InternalType\DictionaryObject();
        $leafAction->Type = new InternalType\NameObject('Action');
        $leafAction->S    = new InternalType\NameObject('GoTo3DView');
        $action2Dictionary->Next->items[] = $leafAction;

        $action = Action\AbstractAction::load($dictionary);

        $actionsToClean        = array();
        $deletionCandidateKeys = array();
        $iterator = new \RecursiveIteratorIterator($action, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $chainedAction) {
            if ($chainedAction instanceof Action\GoToAction) {
                $actionsToClean[]        = $iterator->getSubIterator();
                $deletionCandidateKeys[] = $iterator->getSubIterator()->key();
            }
        }
        foreach ($actionsToClean as $id => $action) {
            unset($action->next[$deletionCandidateKeys[$id]]);
        }
        $actionsCount = 0;
        $iterator = new \RecursiveIteratorIterator(new Util\RecursivelyIteratableObjectsContainer(array($action)),
                                                  \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $chainedAction) {
            $actionsCount++;
        }
        $this->assertEquals(18, $actionsCount);

        $action->dumpAction(new ObjectFactory(1));
        $this->assertEquals(
            $action->getResource()->toString(),
            '<</Type /Action '
            . '/S /Thread '
            . '/D (NamedDestination 2) '
            . '/Next [1 0 R 2 0 R 3 0 R 4 0 R 5 0 R 6 0 R 7 0 R 8 0 R 9 0 R 10 0 R 11 0 R 12 0 R 13 0 R 14 0 R 15 0 R 16 0 R 17 0 R ] >>');
    }

    public function testCreate()
    {
        $action1 = Action\GoToAction::create('SomeNamedDestination');
        $action1->next[] = Action\GoToAction::create('AnotherNamedDestination');

        $action1->dumpAction(new ObjectFactory(1));

        $this->assertEquals($action1->getResource()->toString(),
                            '<</Type /Action /S /GoTo /D (SomeNamedDestination) /Next 1 0 R >>');
    }

    public function testCreate1()
    {
        $pdf = new Pdf\PdfDocument();
        $page1 = $pdf->newPage(Pdf\Page::SIZE_A4);
        $page2 = $pdf->newPage(Pdf\Page::SIZE_A4);

        $destination = Destination\Fit::create($page2);

        $action = Action\GoToAction::create($destination);

        $action->dumpAction(new ObjectFactory(1));

        $this->assertEquals($action->getResource()->toString(),
                            '<</Type /Action /S /GoTo /D [4 0 R /Fit ] >>');
    }

    public function testGetDestination()
    {
        $dictionary = new InternalType\DictionaryObject();
        $dictionary->Type = new InternalType\NameObject('Action');
        $dictionary->S    = new InternalType\NameObject('GoTo');
        $dictionary->D    = new InternalType\StringObject('SomeNamedDestination');

        $action = Action\AbstractAction::load($dictionary);

        $this->assertEquals($action->getDestination()->getName(), 'SomeNamedDestination');
    }

    public function testGetDestination2()
    {
        $pdf = new Pdf\PdfDocument();
        $page1 = $pdf->newPage(Pdf\Page::SIZE_A4);
        $page2 = $pdf->newPage(Pdf\Page::SIZE_A4);
        $page3 = $pdf->newPage(Pdf\Page::SIZE_A4);  // Page created, but not included into pages list

        $pdf->pages[] = $page1;
        $pdf->pages[] = $page2;

        $action1 = Action\GoToAction::create(Destination\Fit::create($page2));
        $action2 = Action\GoToAction::create(Destination\Fit::create($page3));

        $this->assertTrue($pdf->resolveDestination($action1->getDestination()) === $page2);
        $this->assertTrue($pdf->resolveDestination($action2->getDestination()) === null);
    }

    public function testActionURILoad1()
    {
        $dictionary = new InternalType\DictionaryObject();
        $dictionary->Type = new InternalType\NameObject('Action');
        $dictionary->S    = new InternalType\NameObject('URI');
        $dictionary->URI  = new InternalType\StringObject('http://somehost/');

        $action = Action\AbstractAction::load($dictionary);

        $this->assertTrue($action instanceof Action\URI);
    }

    public function testActionURILoad2()
    {
        $dictionary = new InternalType\DictionaryObject();
        $dictionary->Type = new InternalType\NameObject('Action');
        $dictionary->S    = new InternalType\NameObject('URI');


        $this->setExpectedException('\Zend\Pdf\Exception\CorruptedPdfException', 'URI action dictionary entry is required');
        $action = Action\AbstractAction::load($dictionary);

    }

    public function testActionURICreate()
    {
        $action = Action\URI::create('http://somehost/');

        $this->assertTrue($action instanceof Action\URI);

        $this->assertEquals($action->getResource()->toString(),
                            '<</Type /Action /S /URI /URI (http://somehost/) >>');
    }

    public function testActionURIGettersSetters()
    {
        $action = Action\URI::create('http://somehost/');

        $this->assertEquals($action->getUri(), 'http://somehost/');

        $action->setUri('http://another_host/');
        $this->assertEquals($action->getUri(), 'http://another_host/');

        $this->assertEquals($action->getIsMap(), false);

        $action->setIsMap(true);
        $this->assertEquals($action->getIsMap(), true);
        $this->assertEquals($action->getResource()->toString(),
                            '<</Type /Action /S /URI /URI (http://another_host/) /IsMap true >>');

        $action->setIsMap(false);
        $this->assertEquals($action->getIsMap(), false);
        $this->assertEquals($action->getResource()->toString(),
                            '<</Type /Action /S /URI /URI (http://another_host/) >>');
    }

    /**
     * @group ZF-8462
     */
    public function testPhpVersionBug()
    {
        $this->setExpectedException(
            '\Zend\Pdf\Exception\NotImplementedException',
            'Cross-reference streams are not supported yet'
        );

        $pdf = Pdf\PdfDocument::load(__DIR__ . '/_files/ZF-8462.pdf');
    }
}
