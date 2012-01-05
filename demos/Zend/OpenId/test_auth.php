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
set_include_path("$dir/incubator/library" . PATH_SEPARATOR . "$dir/library" . PATH_SEPARATOR . get_include_path());

/**
 * @see Zend_Auth
 */
require_once "Zend/Auth.php";

/**
 * @see Zend_Auth_Adapter_OpenId
 */
require_once "Zend/Auth/Adapter/OpenId.php";

$status = "";
$auth = Zend_Auth::getInstance();
if ((isset($_POST['openid_action']) &&
     $_POST['openid_action'] == "login" &&
     !empty($_POST['openid_identifier'])) ||
    isset($_GET['openid_mode']) ||
    isset($_POST['openid_mode'])) {
    $result = $auth->authenticate(
    new Zend_Auth_Adapter_OpenId(@$_POST['openid_identifier']));
    if ($result->isValid()) {
        Zend_OpenId::redirect(Zend_OpenId::selfURL());
    } else {
        $auth->clearIdentity();
        foreach ($result->getMessages() as $message) {
            $status .= "$message<br>\n";
        }
    }
} else if ($auth->hasIdentity()) {
    if (isset($_POST['openid_action']) &&
        $_POST['openid_action'] == "logout") {
        $auth->clearIdentity();
    } else {
        $status = "You are logged-in as " . $auth->getIdentity() . "<br>\n";
    }
}
?>
<html><body>
<?php echo "$status";?>
<form method="post"><fieldset>
<legend>OpenID Login</legend>
<input type="text" name="openid_identifier" value="">
<input type="submit" name="openid_action" value="login">
<input type="submit" name="openid_action" value="logout">
</fieldset></form></body></html>
