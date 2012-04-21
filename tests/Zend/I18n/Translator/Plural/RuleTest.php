<?php

namespace ZendTest\I18n\Translator\Plural;

use \PHPUnit_Framework_TestCase as TestCase;
use Zend\I18n\Translator\Plural\Rule;

class RuleTest extends TestCase
{
    public function testSimpleParse()
    {
        var_dump(Rule::fromString('!5 * (2 + 3)')->evaluate(4));
    }
}
