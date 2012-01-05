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
 * This sample file demonstrates an advanced use case of Zend_ProgressBar with
 * Zend_Form and Zend_File_Transfer.
 */

set_include_path(realpath(__DIR__ . '/../../../library')
                 . PATH_SEPARATOR . get_include_path());

if (isset($_GET['progress_key'])) {
    require_once 'Zend/File/Transfer/Adapter/Http.php';
    require_once 'Zend/ProgressBar.php';
    require_once 'Zend/ProgressBar/Adapter/JsPull.php';

    $adapter = new Zend_ProgressBar_Adapter_JsPull();
    Zend_File_Transfer_Adapter_Http::getProgress(array('progress' => $adapter));
    die;
}
?>
<html>
<head>
    <title>Zend_ProgressBar Upload Demo</title>
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
            top: 120px;
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
        function makeRequest(url)
        {
            var httpRequest;

            if (window.XMLHttpRequest) {
                httpRequest = new XMLHttpRequest();
                if (httpRequest.overrideMimeType) {
                    httpRequest.overrideMimeType('text/xml');
                }
            } else if (window.ActiveXObject) {
                try {
                    httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
                } catch (e) {
                    try {
                        httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                    } catch (e) {}
                }
            }

            if (!httpRequest) {
                alert('Giving up :( Cannot create an XMLHTTP instance');
                return false;
            }

            httpRequest.onreadystatechange = function() { evalProgress(httpRequest); };
            httpRequest.open('GET', url, true);
            httpRequest.send('');

        }

        function observeProgress()
        {
            setTimeout("getProgress()", 1500);
        }

        function getProgress()
        {
            makeRequest('ZendForm.php?progress_key=' + document.getElementById('progress_key').value);
        }

        function evalProgress(httpRequest)
        {
            try {
                if (httpRequest.readyState == 4) {
                    if (httpRequest.status == 200) {
                        eval('var data = ' + httpRequest.responseText);

                        if (data.finished) {
                            finish();
                        } else {
                            update(data);
                            setTimeout("getProgress()", 1000);
                        }
                    } else {
                        alert('There was a problem with the request.');
                    }
                }
            } catch(e) {
                alert('Caught Exception: ' + e.description);
            }
        }

        function update(data)
        {
            document.getElementById('pg-percent').style.width = data.percent + '%';

            document.getElementById('pg-text-1').innerHTML = data.text;
            document.getElementById('pg-text-2').innerHTML = data.text;
        }

        function finish()
        {
            document.getElementById('pg-percent').style.width = '100%';

            document.getElementById('pg-text-1').innerHTML = 'Upload done';
            document.getElementById('pg-text-2').innerHTML = 'Upload done';
        }
    </script>
</head>
<body>
    <?php
    require_once 'Zend/View.php';
    require_once 'Zend/Form.php';

    $form = new Zend_Form(array(
        'enctype'  => 'multipart/form-data',
        'action'   => 'ZendForm.php',
        'target'   => 'uploadTarget',
        'onsubmit' => 'observeProgress();',
        'elements' => array(
            'file'   => array('file', array('label' => 'File')),
            'submit' => array('submit', array('label' => 'Upload!'))
        )
    ));

    $form->setView(new Zend_View());

    echo $form;
    ?>
    <iframe name="uploadTarget"></iframe>

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

