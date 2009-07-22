<?php
/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
 */


/** Zend_Pdf_Action */
require_once 'Zend/Pdf/Action.php';

/** Zend_Pdf_Action */
require_once 'Zend/Pdf/ElementFactory.php';

/** Zend_Pdf */
require_once 'Zend/Pdf.php';


/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Pdf
 * @subpackage UnitTests
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

        $this->assertEquals(20, count($action->getAllActions()));

        $action->clean();
    }

    public function testExtract()
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

        foreach ($action->getAllActions() as $action) {
            if ($action instanceof Zend_Pdf_Action_Thread) {
                $root = $action->extract();
            }
        }
        $this->assertTrue($root instanceof Zend_Pdf_Action_GoTo);
        $this->assertEquals(18, count($root->getAllActions()));

        foreach ($root->getAllActions() as $action) {
            if ($action instanceof Zend_Pdf_Action_Goto) {
                $root = $action->extract();
            }
        }
        $this->assertTrue($root instanceof Zend_Pdf_Action_GoToR);
        $this->assertEquals(16, count($root->getAllActions()));

        $root->rebuildSubtree();

        $this->assertEquals(
            $root->getResource()->toString(),
            '<</Type /Action /S /GoToR '
            . "/Next [<</Type /Action /S /GoToE >> <</Type /Action /S /Launch >> <</Type /Action /S /URI >> <</Type /Action /S /Sound >> <</Type /Action /S /Movie >> \n"
            .        "<</Type /Action /S /Hide >> <</Type /Action /S /Named >> <</Type /Action /S /SubmitForm >> <</Type /Action /S /ResetForm >> <</Type /Action /S /ImportData >> \n"
            .        "<</Type /Action /S /JavaScript >> <</Type /Action /S /SetOCGState >> <</Type /Action /S /Rendition >> <</Type /Action /S /Trans >> \n"
            .        '<</Type /Action /S /GoTo3DView >> ] >>');

        $root->clean();
    }

    public function testAttach()
    {
        $action1Dictionary = new Zend_Pdf_Element_Dictionary();
        $action1Dictionary->Type = new Zend_Pdf_Element_Name('Action');
        $action1Dictionary->S    = new Zend_Pdf_Element_Name('GoTo');
        $action1Dictionary->D    = new Zend_Pdf_Element_String('Destination 1');
        $action1 = Zend_Pdf_Action::load($action1Dictionary);


        $action2Dictionary = new Zend_Pdf_Element_Dictionary();
        $action2Dictionary->Type = new Zend_Pdf_Element_Name('Action');
        $action2Dictionary->S    = new Zend_Pdf_Element_Name('Thread');
        $action2Dictionary->D    = new Zend_Pdf_Element_String('Destination 2');
        $action2Dictionary->Next = new Zend_Pdf_Element_Array();

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('GoTo');
        $leafAction->D    = new Zend_Pdf_Element_String('Destination 3');
        $action2Dictionary->Next->items[] = $leafAction;

        $leafAction = new Zend_Pdf_Element_Dictionary();
        $leafAction->Type = new Zend_Pdf_Element_Name('Action');
        $leafAction->S    = new Zend_Pdf_Element_Name('GoToR');
        $action2Dictionary->Next->items[] = $leafAction;

        $action2 = Zend_Pdf_Action::load($action2Dictionary);

        $action1->attach($action2);
        $action1->rebuildSubtree();

        $this->assertEquals(
            $action1->getResource()->toString(),
            '<</Type /Action /S /GoTo /D (Destination 1) '
            . '/Next <</Type /Action /S /Thread /D (Destination 2) '
            .         '/Next [<</Type /Action /S /GoTo /D (Destination 3) >> <</Type /Action /S /GoToR >> ] >> >>');

        $action1->clean();
    }

    public function testCreate()
    {
    	$action1 = Zend_Pdf_Action_GoTo::create('SomeNamedDestination');
    	$action1->attach(Zend_Pdf_Action_GoTo::create('AnotherNamedDestination'));

    	$action1->rebuildSubtree();

    	$this->assertEquals($action1->getResource()->toString(),
    	                    '<</Type /Action /S /GoTo /D (SomeNamedDestination) /Next <</Type /Action /S /GoTo /D (AnotherNamedDestination) >> >>');

    	$action1->clean();
    }

    public function testCreate1()
    {
    	$pdf = new Zend_Pdf();
    	$page1 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
    	$page2 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);

    	require_once 'Zend/Pdf/Destination/Fit.php';
    	$destination = Zend_Pdf_Destination_Fit::create($page2);

        $action = Zend_Pdf_Action_GoTo::create($destination);
        $action->rebuildSubtree();

        $this->assertEquals($action->getResource()->toString(),
                            '<</Type /Action /S /GoTo /D [4 0 R /Fit ] >>');

        $action->clean();
    }

    public function testGetDestination()
    {
        $dictionary = new Zend_Pdf_Element_Dictionary();
        $dictionary->Type = new Zend_Pdf_Element_Name('Action');
        $dictionary->S    = new Zend_Pdf_Element_Name('GoTo');
        $dictionary->D    = new Zend_Pdf_Element_String('SomeNamedDestination');

        $action = Zend_Pdf_Action::load($dictionary);

        $this->assertEquals($action->getDestination(), 'SomeNamedDestination');

        $action->clean();
    }

    public function testGetDestination2()
    {
        $pdf = new Zend_Pdf();
        $page1 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
        $page2 = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);

        require_once 'Zend/Pdf/Destination/Fit.php';
        $destination = Zend_Pdf_Destination_Fit::create($page2);

        $action = Zend_Pdf_Action_GoTo::create($destination);

        $this->assertTrue($action->getDestination() == $destination);

        $action->clean();
    }
}
