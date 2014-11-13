<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Dom;

/**
 * Transform CSS selectors to XPath
 *
 * @deprecated
 * @see Document\Query
 */
class Css2Xpath
{
    /**
     * Transform CSS expression to XPath
     *
     * @deprecated
     * @see Document\Query
     * @param  string $path
     * @return string
     */
    public static function transform($path)
    {
        trigger_error(sprintf('%s is deprecated; please use %s\Document\Query::cssToXpath instead', __METHOD__, __NAMESPACE__), E_USER_DEPRECATED);
        return Document\Query::cssToXpath($path);
    }
}
