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
 * @package    Zend_Search_Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Search\Lucene\Analysis\TokenFilter;

use Zend\Search\Lucene\Analysis\Token,
    Zend\Search\Lucene,
    Zend\Search\Lucene\Exception\ExtensionNotLoadedException;

/**
 * Lower case Token filter.
 *
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Analysis
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

