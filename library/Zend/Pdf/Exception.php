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
 * @package    Zend_PDF
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Pdf;

/**
 * Exception interface for Zend\Pdf.
 *
 * If you expect a certain type of exception to be caught and handled by the
 * caller, create a constant for it here and include it in the object being
 * thrown. Example:
 *
 *   throw new \Zend\Exception\CorruptedPdfException('foo() is not yet implemented',
 *                                \Zend\Exception\CorruptedPdfException::NOT_IMPLEMENTED);
 *
 * This allows the caller to determine the specific type of exception that was
 * thrown without resorting to parsing the descriptive text.
 *
 * IMPORTANT: Do not rely on numeric values of the constants! They are grouped
 * sequentially below for organizational purposes only. The numbers may come to
 * mean something in the future, but they are subject to renumbering at any
 * time. ALWAYS use the symbolic constant names, which are guaranteed never to
 * change, in logical checks! You have been warned.
 *
 * @package    Zend_PDF
 * @subpackage Core
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Exception
{
}

