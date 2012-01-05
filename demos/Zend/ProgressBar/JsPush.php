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
 * @package    Zend_ProgressBar
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * This sample file demonstrates a simple use case of a jspush-driven progressbar
 */

if (isset($_GET['progress'])) {
    set_include_path(realpath(__DIR__ . '/../../../library')
                     . PATH_SEPARATOR . get_include_path());

    require_once 'Zend/ProgressBar.php';
    require_once 'Zend/ProgressBar/Adapter/JsPush.php';

    $adapter     = new Zend_ProgressBar_Adapter_JsPush(array('updateMethodName' => 'Zend_ProgressBar_Update',
                                                             'finishMethodName' => 'Zend_ProgressBar_Finish'));
    $progressBar = new Zend_ProgressBar($adapter, 0, 100);

    for ($i = 1; $i <= 100; $i++) {
        if ($i < 20) {
            $text = 'Just beginning';
        } else if ($i < 50) {
            $text = 'A bit done';
        } else if ($i < 80) {
            $text = 'Getting closer';
        } else {
            $text = 'Nearly done';
        }

        $progressBar->update($i, $text);
        usleep(100000);
    }

    $progressBar->finish();

    die;
}
?>
<html>
<head>
    <title>Zend_ProgressBar Javascript Push Demo and Test</title>
    <style type="text/css">
        iframe {
            position: absolute;
            left: -100px;
            top: -100px;

            width: 10px;
            height: 10px;
            overflow: hidden;
        }

        #progressbar {
            position: absolute;
            left: 10px;
            top: 10px;
        }

        .pg-progressbar {
            position: relative;

            width: 250px;
            height: 24px;
            overflow: hidden;

            border: 1px solid #c6c6c6;
        }

        .pg-progress {
            z-index: 150;

            position: absolute;
            left: 0;
            top: 0;

            width: 0;
            height: 24px;
            overflow: hidden;
        }

        .pg-progressstyle {
            height: 22px;

            border: 1px solid #748a9e;
            background-image: url('animation.gif');
        }

        .pg-text,
        .pg-invertedtext {
            position: absolute;
            left: 0;
            top: 4px;

            width: 250px;

            text-align: center;
            font-family: sans-serif;
            font-size: 12px;
        }

        .pg-invertedtext {
            color: #ffffff;
        }

        .pg-text {
            z-index: 100;
            color: #000000;
        }
    </style>
    <script type="text/javascript">
        function startProgress()
        {
            var iFrame = document.createElement('iframe');
            document.getElementsByTagName('body')[0].appendChild(iFrame);
            iFrame.src = 'JsPush.php?progress';
        }

        function Zend_ProgressBar_Update(data)
        {
            document.getElementById('pg-percent').style.width = data.percent + '%';

            document.getElementById('pg-text-1').innerHTML = data.text;
            document.getElementById('pg-text-2').innerHTML = data.text;
        }

        function Zend_ProgressBar_Finish()
        {
            document.getElementById('pg-percent').style.width = '100%';

            document.getElementById('pg-text-1').innerHTML = 'Demo done';
            document.getElementById('pg-text-2').innerHTML = 'Demo done';
        }
    </script>
</head>
<body onload="startProgress();">
    <div id="progressbar">
        <div class="pg-progressbar">
            <div class="pg-progress" id="pg-percent">
                <div class="pg-progressstyle"></div>
                <div class="pg-invertedtext" id="pg-text-1"></div>
            </div>
            <div class="pg-text" id="pg-text-2"></div>
        </div>
    </div>
    <div id="progressBar"><div id="progressDone"></div></div>
</body>
</html>