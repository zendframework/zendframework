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
 * @package    Zend_Translator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

return array(
    '' => array(
        'plural_forms' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);'
    ),
    'Message 1' => 'Message 1 (en)',
    'Message 2' => 'Message 2 (en)',
    'Message 3' => 'Message 3 (en)',
    'Message 4' => 'Message 4 (en)',
    'Message 5' => array(
        0 => 'Message 5 (en) Plural 0',
        1 => 'Message 5 (en) Plural 1',
        2 => 'Message 5 (en) Plural 2'
    ),
    'Cooking furniture' => 'Küchen Möbel (en)',
    'Küchen Möbel' => 'Cooking furniture (en)',
);
