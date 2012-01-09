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
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Mock file for testbed
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ViewController extends \Zend\Controller\Action
{

    /**
     * Test Function for indexAction
     *
     * @return void
     */
    public function indexAction()
    {
        $this->render();
    }

    /**
     * Test Function for testAction
     *
     * @return void
     */
    public function testAction()
    {
        $this->render('index');
    }

    /**
     * Test Function for siteAction
     *
     * @return void
     */
    public function siteAction()
    {
        $this->render('site', null, true);
    }

    /**
     * Test Function for nameAction
     *
     * @return void
     */
    public function nameAction()
    {
        $this->render(null, 'name');
    }

    /**
     * Test Function for scriptAction
     *
     * @return void
     */
    public function scriptAction()
    {
        $this->renderScript('custom/renderScript.php');
    }

    /**
     * Test Function for scriptNameAction
     *
     * @return void
     */
    public function scriptNameAction()
    {
        $this->renderScript('custom/renderScript.php', 'foo');
    }

}
