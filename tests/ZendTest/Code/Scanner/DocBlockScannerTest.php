<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace ZendTest\Code\Scanner;

use Zend\Code\Scanner\DocBlockScanner;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   Zend
 * @package    Zend_Code_Scanner
 * @subpackage UnitTests
 * @group      Zend_Code_Scanner
 */
class DocBlockScannerTest extends TestCase
{
    /**
     * @group ZF2-110
     */
    public function testDocBlockScannerParsesTagsWithNoValuesProperly()
    {
        $docComment = <<<EOB
/**
 * @mytag
 */
EOB;
        $tokenScanner = new DocBlockScanner($docComment);
        $tags = $tokenScanner->getTags();
        $this->assertCount(1, $tags);
        $this->assertArrayHasKey('name', $tags[0]);
        $this->assertEquals('@mytag', $tags[0]['name']);
        $this->assertArrayHasKey('value', $tags[0]);
        $this->assertEquals('', $tags[0]['value']);
    }

    public function testDocBlockScannerDescriptions()
    {
        $docComment = <<<EOB
/**
 * Short Description
 *
 * Long Description
 * continued in the second line
 */
EOB;
        $tokenScanner = new DocBlockScanner($docComment);
        $this->assertEquals('Short Description', $tokenScanner->getShortDescription());
        $this->assertEquals('Long Description continued in the second line', $tokenScanner->getLongDescription());

        // windows-style line separators
        $docComment = str_replace("\n", "\r\n", $docComment);
        $tokenScanner = new DocBlockScanner($docComment);
        $this->assertEquals('Short Description', $tokenScanner->getShortDescription());
        $this->assertEquals('Long Description continued in the second line', $tokenScanner->getLongDescription());
    }
}
