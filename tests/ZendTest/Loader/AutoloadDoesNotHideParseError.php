<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Loader
 */

Zend_Loader::registerAutoload();

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/_files');

$parseError = new ParseError();
