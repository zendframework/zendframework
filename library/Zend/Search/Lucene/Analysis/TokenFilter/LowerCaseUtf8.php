<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Search
 */

namespace Zend\Search\Lucene\Analysis\TokenFilter;

use Zend\Search\Lucene;
use Zend\Search\Lucene\Analysis\Token;
use Zend\Search\Lucene\Exception\ExtensionNotLoadedException;

/**
 * Lower case Token filter.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Analysis
 */
class LowerCaseUtf8 implements TokenFilterInterface
{
    /**
     * Object constructor
     * @throws \Zend\Search\Lucene\Exception\ExtensionNotLoadedException
     */
    public function __construct()
    {
        if (!function_exists('mb_strtolower')) {
            // mbstring extension is disabled
            throw new ExtensionNotLoadedException('Utf8 compatible lower case filter needs mbstring extension to be enabled.');
        }
    }

    /**
     * Normalize Token or remove it (if null is returned)
     *
     * @param \Zend\Search\Lucene\Analysis\Token $srcToken
     * @return \Zend\Search\Lucene\Analysis\Token
     */
    public function normalize(Token $srcToken)
    {
        $newToken = new Token(mb_strtolower($srcToken->getTermText(), 'UTF-8'),
                                       $srcToken->getStartOffset(),
                                       $srcToken->getEndOffset());

        $newToken->setPositionIncrement($srcToken->getPositionIncrement());

        return $newToken;
    }
}

