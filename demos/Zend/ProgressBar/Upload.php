<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ProgressBar
 */

use Zend\Loader\StandardAutoloader;
use Zend\ProgressBar\Adapter\JsPull;
use Zend\ProgressBar\ProgressBar;

/**
 * This sample file demonstrates a simple use case of a jspull-driven progressbar
 */

if (isset($_GET['uploadId'])) {
    require_once dirname(dirname(dirname(__DIR__))) . '/library/Zend/Loader/StandardAutoloader.php';
    $loader = new StandardAutoloader(array('autoregister_zf' => true));
    $loader->register();

    $data          = uploadprogress_get_info($_GET['uploadId']);
    $bytesTotal    = ($data === null ? 0 : $data['bytes_total']);
    $bytesUploaded = ($data === null ? 0 : $data['bytes_uploaded']);

    $adapter     = new JsPull();
    $progressBar = new ProgressBar($adapter, 0, $bytesTotal, 'uploadProgress');

    if ($bytesTotal === $bytesUploaded) {
        $progressBar->finish();
    } else {
        $progressBar->update($bytesUploaded);
    }
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
            top: 50px;
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
            makeRequest('Upload.php?uploadId=' + document.getElementById('uploadId').value);
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

            document.getElementById('pg-text-1').innerHTML = data.timeRemaining + ' seconds remaining';
            document.getElementById('pg-text-2').innerHTML = data.timeRemaining + ' seconds remaining';
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
    <form enctype="multipart/form-data" method="post" action="Upload.php" target="uploadTarget"
          onsubmit="observeProgress();">
        <input type="hidden" name="UPLOAD_IDENTIFIER" id="uploadId" value="<?php echo md5(uniqid(rand())); ?>"/>
        <input type="file" name="file"/>
        <input type="submit" value="Upload!"/>
    </form>
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
