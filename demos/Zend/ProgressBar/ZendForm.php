<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ProgressBar
 */

use Zend\File\Transfer\Adapter\Http;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Form\View\Helper;
use Zend\Loader\StandardAutoloader;
use Zend\ProgressBar\Adapter\JsPull;

/**
 * This sample file demonstrates an advanced use case of Zend_ProgressBar with
 * Zend_Form and Zend_File_Transfer.
 */

require_once dirname(dirname(dirname(__DIR__))) . '/library/Zend/Loader/StandardAutoloader.php';
$loader = new StandardAutoloader(array('autoregister_zf' => true));
$loader->register();

if (isset($_GET['progress_key'])) {
    $adapter = new JsPull();
    Http::getProgress(array('progress' => $adapter));
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
                    } catch (e) {
                    }
                }
            }

            if (!httpRequest) {
                alert('Giving up :( Cannot create an XMLHTTP instance');
                return false;
            }

            httpRequest.onreadystatechange = function () {
                evalProgress(httpRequest);
            };
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
            } catch (e) {
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
$file  = new Element\File('file');
$file->setLabel('File');

$progress_key  = new Element\Hidden('progress_key');
$progress_key->setAttribute('id', 'progress_key');
$progress_key->setValue(md5(uniqid(rand())));

$submit  = new Element\Submit('submit');
$submit->setValue('Upload!');

$form = new Form("ZendForm");
$form->setAttributes(array(
    'enctype'  => 'multipart/form-data',
    'action'   => 'ZendForm.php',
    'target'   => 'uploadTarget',
    'onsubmit' => 'observeProgress();'
));

$form->prepare();

$formhelper   = new Helper\Form();
$formfile     = new Helper\FormFile();
$formhidden   = new Helper\FormHidden();
$formsubmit   = new Helper\FormSubmit();

echo $formhelper->openTag($form);
echo $formhidden($progress_key);
echo $formfile($file);
echo $formsubmit($submit);
echo $formhelper->closeTag();
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
<div id="progressBar">
    <div id="progressDone"></div>
</div>
</body>
</html>
