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
 * @package    Zend_OpenId
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

$dir = realpath(__DIR__."/../../..");
set_include_path("$dir/library" . PATH_SEPARATOR . get_include_path());

/**
 * @see Zend_OpenId_Provider
 */
require_once "Zend/OpenId/Provider.php";

/**
 * @see Zend_OpenId_Extension_Sreg
 */
require_once "Zend/OpenId/Extension/Sreg.php";

/**
 * @see Zend_Session_Namespace
 */
require_once "Zend/Session/Namespace.php";

$server = new Zend_OpenId_Provider();

/**
 * trust_form
 *
 * @param  string $site
 * @param  array|boolean $trusted
 * @return string
 */
function trust_form($site, $trusted) {
    if (is_array($trusted)) {
        $str = "";
        if (isset($trusted['Zend_OpenId_Extension_Sreg'])) {
            $trusted = $trusted['Zend_OpenId_Extension_Sreg'];
            foreach ($trusted as $key => $val) {
                $str .= "$key:\"$val\";";
            }
        }
        $trusted = true;
    }
    $s = '<form method="POST">'
       . '<tr><td>'
       . '<input type="hidden" name="openid_action" value="trust">'
       . '<input type="hidden" name="site" value="' . $site . '">'
       . $site
//       . '</td><td>'
//     . ($trusted ? 'allowed' : 'denied')
       . '</td><td>'
       . ($trusted ?
          '<input type="submit" style="width:100px" name="deny" value="Deny">' :
          '<input type="submit" style="width:100px" name="allow" value="Allow">')
       . '</td><td>'
       .  '<input type="submit" style="width:100px" name="del" value="Del">'
       . '</td><td>'.$str.'</td></tr>'
       . '</form>';
    return $s;
}

/**
 * sreg_form
 *
 * @param  Zend_OpenId_Extension_Sreg $sreg
 * @return string
 */
function sreg_form(Zend_OpenId_Extension_Sreg $sreg)
{
    $s = "";
    $props = $sreg->getProperties();
    if (is_array($props) && count($props) > 0) {
        $s = 'It also requests additinal information about you';
        $s .= ' (fields marked by <u>*</u> are required)<br>';
        $s .= '<table border="0" cellspacing="2" cellpadding="2">';
        foreach ($props as $prop => $val) {
            if ($val) {
                $s .= '<tr><td><u>'.$prop.':*</u></td>';
            } else {
                $s .= '<tr><td>'.$prop.':</u></td>';
            }
            $value = "";
            $s .= '<td><input type="text" name="openid.sreg.'.$prop.'" value="'.$value.'"></td></tr>';
        }
        $s .= '</table><br>';
        $policy = $sreg->getPolicyUrl();
        if (!empty($policy)) {
            $s .= 'The private policy can be found at <a href="'.$policy.'">'.$policy.'</a>.<br>';
        }
    }
    return $s;
}

$session = new Zend_Session_Namespace("opeinid.server");
Zend_Session::start();

$ret = false;
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET['openid_action']) && isset($_GET['openid_mode'])) {
        $ret = $server->handle($_GET, new Zend_OpenId_Extension_Sreg());
    } else {
        require_once 'Zend/View.php';

        $view = new Zend_View();
        $view->setScriptPath(__DIR__ . '/templates');
        $view->strictVars(true);

        if (isset($session->id)) {
            $view->id = $session->id;
        }
        if (isset($session->error)) {
            $view->error = $session->error;
            unset($session->error);
        }
        if (isset($_GET['openid_action'])) {
            if ($_GET['openid_action'] == 'register') {
                $ret = $view->render('register.phtml');
            } else if ($_GET['openid_action'] == 'registration_complete' &&
                       isset($_GET['openid_name'])) {
                $view->name = $_GET['openid_name'];
                $view->url = Zend_OpenId::selfURL() . '?openid=' . $view->name;
                if ($server->hasUser($view->url)) {
                    $view->url2 = Zend_OpenId::selfURL() . '?openid2=' . $view->name;
                    $ret = $view->render('registration_complete.phtml');
                }
            } else if ($_GET['openid_action'] == 'logout') {
                $server->logout();
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else if ($_GET['openid_action'] == 'login') {
                if (isset($_GET['openid_identity'])) {
                    $view->id = $_GET['openid_identity'];
                    $view->ro = true;
                }
                $ret = $view->render('login.phtml');
            } else if ($_GET['openid_action'] == 'trust') {
                if ($server->getLoggedInUser() !== false) {
                    $view->site = $server->getSiteRoot($_GET);
                    $view->url = $server->getLoggedInUser();
                    $sreg = new Zend_OpenId_Extension_Sreg();
                    $sreg->parseRequest($_GET);
                    $view->sreg = sreg_form($sreg);
                    if ($server->hasUser($view->url)) {
                        $ret = $view->render('trust.phtml');
                    }
                }
            }
        } else if (isset($_GET['openid'])) {
            $url = Zend_OpenId::selfURL() . '?openid=' . $_GET['openid'];
            if ($server->hasUser($url)) {
                $view->server = Zend_OpenId::selfURL();
                $view->name = $_GET['openid'];
                $ret = $view->render('identity.phtml');
            }
        } else if (isset($_GET['openid2'])) {
            $url = Zend_OpenId::selfURL() . '?openid=' . $_GET['openid2'];
            if ($server->hasUser($url)) {
                $view->server = Zend_OpenId::selfURL();
                $view->name = $_GET['openid2'];
                $ret = $view->render('identity2.phtml');
            }
        } else {
            if ($server->getLoggedInUser() !== false) {
                $view->url = $server->getLoggedInUser();
                if ($server->hasUser($view->url)) {
                    $sites = $server->getTrustedSites();
                    $s = "";
                    foreach ($sites as $site => $trusted) {
                        if (is_bool($trusted) || is_array($trusted)) {
                            $s .= trust_form($site, $trusted);
                        }
                    }
                    if (empty($s)) {
                        $s = "<tr><td>None</td></tr>";
                    }
                    $view->sites = $s;
                    $ret = $view->render('profile.phtml');
                }
            } else {
                $ret = $view->render('login.phtml');
            }
        }
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['openid_action']) && isset($_POST['openid_mode'])) {
        $ret = $server->handle($_POST, new Zend_OpenId_Extension_Sreg());
    } else if (isset($_POST['openid_action'])) {
        if ($_POST['openid_action'] == 'login' &&
            isset($_POST['openid_url']) &&
            isset($_POST['openid_password'])) {
            if (!$server->login($_POST['openid_url'],
                                $_POST['openid_password'])) {
                $session->error = 'Wrong identity/password!';
                $session->id = $_POST['openid_url'];
            }
            unset($_GET['openid_action']);
            Zend_OpenId::redirect($_SERVER['PHP_SELF'], $_GET);
        } else if ($_POST['openid_action'] == 'register' &&
                  isset($_POST['openid_name']) &&
                  isset($_POST['openid_password']) &&
                  isset($_POST['openid_password2'])) {

            $url = Zend_OpenId::selfURL() . '?openid=' . $_POST['openid_name'];
            if ($_POST['openid_password'] != $_POST['openid_password2']) {
                $session->name = $_POST['openid_name'];
                $session->error = 'Password mismatch.';
                header('Location: ' . $_SERVER['PHP_SELF'] . '?openid.action=register');
            } else if ($server->register($url, $_POST['openid_password'])) {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?openid.action=registration_complete&openid.name=' . $_POST['openid_name']);
            } else {
                $session->error = 'Registration failed. Try another name.';
                header('Location: ' . $_SERVER['PHP_SELF'] . '?openid.action=register');
            }
            exit;
        } else if ($_POST['openid_action'] == 'trust') {
            if (isset($_GET['openid_return_to'])) {
                $sreg = new Zend_OpenId_Extension_Sreg();
                $sreg->parseResponse($_POST);
                if (isset($_POST['allow'])) {
                    if (isset($_POST['forever'])) {
                        $server->allowSite($server->getSiteRoot($_GET), $sreg);
                    }
                    unset($_GET['openid_action']);
                    $server->respondToConsumer($_GET, $sreg);
                } else if (isset($_POST['deny'])) {
                    if (isset($_POST['forever'])) {
                        $server->denySite($server->getSiteRoot($_GET));
                    }
                    Zend_OpenId::redirect($_GET['openid_return_to'], array('openid.mode'=>'cancel'));
                }
            } else if (isset($_POST['allow'])) {
                $server->allowSite($_POST['site']);
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else if (isset($_POST['deny'])) {
                $server->denySite($_POST['site']);
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else if (isset($_POST['del'])) {
                $server->delSite($_POST['site']);
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
        }
    }
}
if (is_string($ret)) {
    echo $ret;
} else if ($ret !== true) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Forbidden';
}
