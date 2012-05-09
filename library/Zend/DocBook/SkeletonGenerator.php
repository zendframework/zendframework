<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_DocBook
 */

namespace Zend\DocBook;

use DOMDocument;

/**
 * @category   Zend
 * @package    Zend_DocBook
 */
class SkeletonGenerator
{
    /**
     * @var ClassParser
     */
    protected $parser;

    /**
     * Constructor
     *
     * @param  ClassParser $parser
     * @return self
     */
    public function __construct(ClassParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Generate DocBook
     *
     * @return string
     */
    public function generate()
    {
        $baseId = $this->parser->getId();

        $dom               = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('section');
        $root->setAttribute('xmlns', 'http://docbook.org/ns/docbook');
        $root->setAttribute('version', '5.0');
        $root->setAttribute('xml:id', $baseId);

        $info  = $dom->createElement('info');
        $title = $dom->createElement('title', $this->parser->getName());
        $info->appendChild($title);
        $root->appendChild($info);

        $overview = $dom->createElement('section');
        $overview->setAttribute('xml:id', $baseId . '.intro');
        $info  = $dom->createElement('info');
        $title = $dom->createElement('title', 'Overview');
        $info->appendChild($title);
        $overview->appendChild($info);
        $root->appendChild($overview);

        $quickstart = $dom->createElement('section');
        $quickstart->setAttribute('xml:id', $baseId . '.quick-start');
        $info  = $dom->createElement('info');
        $title = $dom->createElement('title', 'Quick Start');
        $info->appendChild($title);
        $quickstart->appendChild($info);
        $root->appendChild($quickstart);

        $options = $dom->createElement('section');
        $options->setAttribute('xml:id', $baseId . '.options');
        $info  = $dom->createElement('info');
        $title = $dom->createElement('title', 'Configuration Options');
        $info->appendChild($title);
        $options->appendChild($info);
        $varlist = $dom->createElement('variablelist');
        $title   = $dom->createElement('title');
        $varlist->appendChild($title);
        $options->appendChild($varlist);
        $root->appendChild($options);

        $methods = $dom->createElement('section');
        $methods->setAttribute('xml:id', $baseId . '.methods');
        $info  = $dom->createElement('info');
        $title = $dom->createElement('title', 'Available Methods');
        $info->appendChild($title);
        $methods->appendChild($info);
        $varlist = $dom->createElement('variablelist');
        foreach ($this->parser->getMethods() as $method) {
            $entry = $dom->createElement('varlistentry');
            $entry->setAttribute('xml:id', $method->getId());

            $term = $dom->createElement('term', $method->getName());

            $item1  = $dom->createElement('listitem');
            $synop  = $dom->createElement('methodsynopsis');
            $mname  = $dom->createElement('methodname', $method->getName());
            $mparam = $dom->createElement('methodparam');
            $fparam = $dom->createElement('funcparams', $method->getPrototype());
            $mparam->appendChild($fparam);
            $synop->appendChild($mname);
            $synop->appendChild($mparam);
            $item1->appendChild($synop);

            $item1->appendChild($dom->createElement('para', $method->getShortDescription()));
            if ($method->getLongDescription()) {
                $item1->appendChild($dom->createElement('para', $method->getLongDescription()));
            }
            $item1->appendChild($dom->createElement('para', 'Returns ' . $method->getReturnType()));

            $entry->appendChild($term);
            $entry->appendChild($item1);

            $varlist->appendChild($entry);
        }
        $methods->appendChild($varlist);
        $root->appendChild($methods);

        $examples = $dom->createElement('section');
        $examples->setAttribute('xml:id', $baseId . '.examples');
        $info  = $dom->createElement('info');
        $title = $dom->createElement('title', 'Examples');
        $info->appendChild($title);
        $examples->appendChild($info);
        $root->appendChild($examples);

        $dom->appendChild($root);

        return $dom->saveXML();
    }
}
