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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Pdf_Action */
require_once 'Zend/Pdf/Action.php';

/** Zend_Pdf_Action */
require_once 'Zend/Pdf/ElementFactory.php';

/** Zend_Pdf */
require_once 'Zend/Pdf.php';

/** Zend_Pdf_RecursivelyIteratableObjectsContainer */
require_once 'Zend/Pdf/RecursivelyIteratableObjectsContainer.php';

/** Zend_Pdf_ElementFactory */
require_once 'Zend/Pdf/ElementFactory.php';


/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Pdf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Pdf
 */
class Zend_Pdf_ActionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        date_default_timezone_set('GMT');
    }

    public function testLoad()
    {
        $dictionary = new Zend_Pdf_Element_Dictionary();
        $dictionary->Type = new Zend_Pdf_Element_Name('Action');
        $dictionary->S    = new Zend_Pdf_Element_Name('GoTo');
        $dictionary->D    = new Zend_Pdf_Element_String('SomeNamedDestination');

        $action2Dictionary = new Zend_Pdf_Element_Dictionary();
        $action2Dictionary->Type = new Zend_Pdf_Element_Name('Action');
        $action2Dictionary->S    = new Zend_Pdf_Element_Name('Thread');
        $action2Dictionary->D    = new Zend_Pdf_Element_String('NamedDestination 2');
        $action2Dictionary->Next = new Zend_Pdf_Element_Array();

        $dictionary->Next = $action2Dictionary;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('GoTo');
        $leafAction->D    = new Zend_Pdf_Element_String('NamedDestination 3');
        $action2Dictionary->Next->items[] = $leafAction;


        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('GoToR');
        $action2Dictionary->Next->items[] = $leafAction;


        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('GoToE');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Launch');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Thread');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('URI');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Sound');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Movie');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Hide');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Named');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('SubmitForm');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('ResetForm');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('ImportData');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('JavaScript');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('SetOCGState');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Rendition');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Trans');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('GoTo3DView');
        $action2Dictionary->Next->items[] = $leafAction;

        $action = Zend_Pdf_Action::load($dictionary);

        $actionsCount = 0;
        $iterator = new RecursiveIteratorIterator(new Zend_Pdf_RecursivelyIteratableObjectsContainer(array($action)),
                                                  RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $chainedAction) {
            $actionsCount++;
        }

        $this->assertEquals(20, $actionsCount);
    }

    public function testExtract()
    {
        $dictionary = new Zend_Pdf_Element_Dictionary();
        $dictionary->Type = new Zend_Pdf_Element_Name('Action');
        $dictionary->S    = new Zend_Pdf_Element_Name('GoToR');
        $dictionary->D    = new Zend_Pdf_Element_String('SomeNamedDestination');

        $action2Dictionary = new Zend_Pdf_Element_Dictionary();
        $action2Dictionary->Type = new Zend_Pdf_Element_Name('Action');
        $action2Dictionary->S    = new Zend_Pdf_Element_Name('Thread');
        $action2Dictionary->D    = new Zend_Pdf_Element_String('NamedDestination 2');
        $action2Dictionary->Next = new Zend_Pdf_Element_Array();

        $dictionary->Next = $action2Dictionary;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('GoTo');
        $leafAction->D    = new Zend_Pdf_Element_String('NamedDestination 3');
        $action2Dictionary->Next->items[] = $leafAction;


        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('GoToR');
        $action2Dictionary->Next->items[] = $leafAction;


        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('GoToE');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Launch');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Thread');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('URI');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Sound');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Movie');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Hide');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Named');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('SubmitForm');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('ResetForm');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('ImportData');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('JavaScript');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('SetOCGState');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Rendition');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('Trans');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('GoTo3DView');
        $action2Dictionary->Next->items[] = $leafAction;

        $action = Zend_Pdf_Action::load($dictionary);

        $actionsToClean        = array();
        $deletionCandidateKeys = array();
        $iterator = new RecursiveIteratorIterator($action, RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $chainedAction) {
            if ($chainedAction instanceof Zend_Pdf_Action_GoTo) {
                $actionsToClean[]        = $iterator->getSubIterator();
                $deletionCandidateKeys[] = $iterator->getSubIterator()->key();
            }
        }
        foreach ($actionsToClean as $id => $action) {
            unset($action->next[$deletionCandidateKeys[$id]]);
        }
        $actionsCount = 0;
        $iterator = new RecursiveIteratorIterator(new Zend_Pdf_RecursivelyIteratableObjectsContainer(array($action)),
                                                  RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $chainedAction) {
            $actionsCount++;
        }
        $this->assertEquals(18, $actionsCount);

        $action->dumpAction(new Zend_Pdf_ElementFactory(1));
        $this->assertEquals(
            $action->getResource()->toString(),
            '<</Type /Action '
            . '/S /Thread '
            . '/D (NamedDestination 2) '
            . '/Next [1 0 R 2 0 R 3 0 R 4 0 R 5 0 R 6 0 R 7 0 R 8 0 R 9 0 R 10 0 R 11 0 R 12 0 R 13 0 R 14 0 R 15 0 R 16 0 R 17 0 R ] >>');
    }

    public function testCreate()
    {
        $action1 = Zend_Pdf_Action_GoTo::create('SomeNamedDestination');
        $action1->next[] = Zend_Pdf_Action_GoTo::create('AnotherNamedDestination');

        $action1->dumpAction(new Zend_Pdf_ElementFactory(1));

        $this->assertEquals($action1->getResource()->toString(),
                            '<</Type /Action /S /GoTo /D (SomeNamedDestination) /Next 1 0 R >>');
    }

    public function testCreate1()
    {
        $pdf = new Zend_Pdf();
        $page1 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page2 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);

        require_once 'Zend/Pdf/Destination/Fit.php';
        $destination = Zend_Pdf_Destination_Fit::create($page2);

        $action = Zend_Pdf_Action_GoTo::create($destination);

        $action->dumpAction(new Zend_Pdf_ElementFactory(1));

        $this->assertEquals($action->getResource()->toString(),
                            '<</Type /Action /S /GoTo /D [4 0 R /Fit ] >>');
    }

    public function testGetDestination()
    {
        $dictionary = new Zend_Pdf_Element_Dictionary();
        $dictionary->Type = new Zend_Pdf_Element_Name('Action');
        $dictionary->S    = new Zend_Pdf_Element_Name('GoTo');
        $dictionary->D    = new Zend_Pdf_Element_String('SomeNamedDestination');

        $action = Zend_Pdf_Action::load($dictionary);

        $this->assertEquals($action->getDestination()->getName(), 'SomeNamedDestination');
    }

    public function testGetDestination2()
    {
        $pdf = new Zend_Pdf();
        $page1 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page2 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page3 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);  // Page created, but not included into pages list

        $pdf->pages[] = $page1;
        $pdf->pages[] = $page2;

        require_once 'Zend/Pdf/Destination/Fit.php';
        $action1 = Zend_Pdf_Action_GoTo::create(Zend_Pdf_Destination_Fit::create($page2));
        $action2 = Zend_Pdf_Action_GoTo::create(Zend_Pdf_Destination_Fit::create($page3));

        $this->assertTrue($pdf->resolveDestination($action1->getDestination()) === $page2);
        $this->assertTrue($pdf->resolveDestination($action2->getDestination()) === null);
    }
}
