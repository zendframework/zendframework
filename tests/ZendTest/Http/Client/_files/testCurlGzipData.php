<?php
header('Content-Encoding: gzip');
echo gzcompress('Success');
