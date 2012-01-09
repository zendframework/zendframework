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
 * @package    Zend_Http
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

if (! isset($_GET['redirect'])) $_GET['redirect'] = null;

switch ($_GET['redirect']) {
    case 'abpath':
        header("Location: /path/to/fake/file.ext?redirect=abpath");
        break;

    case 'relpath':
        header("Location: path/to/fake/file.ext?redirect=relpath");
        break;

    default:
        echo "Redirections done.";
        break;
}
