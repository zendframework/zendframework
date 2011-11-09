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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View\Helper;

use Zend\Http\Response,
    Zend\Json\Json as JsonFormatter,
    Zend\Layout\Layout;

/**
 * Helper for simplifying JSON responses
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Json extends AbstractHelper
{
    /**
     * @var Layout
     */
    protected $layout;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Set the layout object
     * 
     * @param  Layout $layout 
     * @return Json
     */
    public function setLayout(Layout $layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Set the response object
     * 
     * @param  Response $response 
     * @return Json
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Encode data as JSON, disable layouts, and set response header
     *
     * If $keepLayouts is true, does not disable layouts.
     *
     * @param  mixed $data
     * @param  bool $keepLayouts
     * NOTE:   if boolean, establish $keepLayouts to true|false
     *         if array, admit params for Zend_Json::encode as enableJsonExprFinder=>true|false
     *         this array can contains a 'keepLayout'=>true|false
     *         that will not be passed to Zend_Json::encode method but will be used here
     * @return string|void
     */
    public function __invoke($data, $keepLayouts = false)
    {
        $options = array();
        if (is_array($keepLayouts))
        {
            $options     = $keepLayouts;
            $keepLayouts = (array_key_exists('keepLayouts', $keepLayouts))
                            ? $keepLayouts['keepLayouts']
                            : false;
            unset($options['keepLayouts']);
        }

        $data = JsonFormatter::encode($data, null, $options);

        if (!$keepLayouts && ($this->layout instanceof Layout)) {
            $this->layout->disableLayout();
        }

        if ($this->response instanceof Response) {
            $headers = $this->response->headers();
            $headers->addHeaderLine('Content-Type', 'application/json');
        }

        return $data;
    }
}
