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
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * PHP sample code for the YouTube data API.  Utilizes the Zend Framework
 * Zend_Gdata component to communicate with the YouTube data API.
 *
 * Requires the Zend Framework Zend_Gdata component and PHP >= 5.1.4
 * This sample is run from within a web browser.  These files are required:
 * session_details.php - a script to view log output and session variables
 * operations.php - the main logic, which interfaces with the YouTube API
 * index.php - the HTML to represent the web UI, contains some PHP
 * video_app.css - the CSS to define the interface style
 * video_app.js - the JavaScript used to provide the video list AJAX interface
 *
 * NOTE: If using in production, some additional precautions with regards
 * to filtering the input data should be used.  This code is designed only
 * for demonstration purposes.
 */
session_start();
?>
<html>
<head>
  <title>YouTube data API Video Browser in PHP - Session Viewer</title>
  <link href="video_app.css" type="text/css" rel="stylesheet"/>
  <script src="video_app.js" type="text/javascript"></script>
</head>
<body>
<div id="mainSessions">
  <div id="titleBar">
  <div id="titleText"><h3>Session variables</h3></div><br clear="all" />
   </div>
<?php

$session_copy = $_SESSION;
ksort($session_copy);

foreach($session_copy as $key => $value) {

    print '<h3>'. $key . '</h3><div id="sessionVariable" >'. $value .'</div><br />'.
        '<form method="POST" action="operations.php">' .
        '<input type="hidden" value="clear_session_var" name="operation"/>'.
        '<input type="hidden" name="name" value="'. $key .'"/>'.
        '<input type="submit" value="click to delete"/></form><hr />';
}
?>
<br clear="both" />
<a href="index.php">back</a>
    </div></body></html>