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
 * @package    Zend_Code
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Code\Annotation\Parser;

use Zend\EventManager\EventInterface;

/**
 * @package    Zend_Code
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface ParserInterface
{
    /**
     * Respond to the "createAnnotation" event
     * 
     * @param  EventInterface $e 
     * @return false|\stdClass
     */
    public function onCreateAnnotation(EventInterface $e);

    /**
     * Register an annotation this parser will accept
     * 
     * @param  mixed $annotation 
     * @return void
     */
    public function registerAnnotation($annotation);

    /**
     * Register multiple annotations this parser will accept
     * 
     * @param  array|\Traversable $annotations 
     * @return void
     */
    public function registerAnnotations($annotations);
}
