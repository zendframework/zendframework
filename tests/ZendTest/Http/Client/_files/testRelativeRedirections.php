<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
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
