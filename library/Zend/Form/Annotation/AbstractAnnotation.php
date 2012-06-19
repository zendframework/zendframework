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
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\Annotation;

use Zend\Code\Annotation\AnnotationInterface;
use Zend\Form\Exception;
use Zend\Json\Exception\ExceptionInterface as JsonException;
use Zend\Json\Json;

/**
 * Base annotation for use with form annotation builder.
 *
 * Provides a stub for initialize(), allowing for "presence only" annotations 
 * (i.e., annotations that define behavior simply by being present). 
 * Additionally, provides a method for parsing the contents of an annotation
 * if provided as a JSON entity.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractAnnotation implements AnnotationInterface
{
    /**
     * Basic annotation; presence only is required
     * 
     * @param  mixed $content 
     * @return void
     */
    public function initialize($content)
    {
    }

    /**
     * Parse and return JSON content
     * 
     * @param  string $content 
     * @return mixed
     * @throws JsonException
     */
    public function parseJsonContent($content)
    {
        $useBuiltIn = Json::$useBuiltinEncoderDecoder;
        Json::$useBuiltinEncoderDecoder = true;
        try {
            $parsed = Json::decode($content, true);
            Json::$useBuiltinEncoderDecoder = $useBuiltIn;
        } catch (JsonException $e) {
            Json::$useBuiltinEncoderDecoder = $useBuiltIn;
            throw $e;
        }
        return $parsed;
    }
}
