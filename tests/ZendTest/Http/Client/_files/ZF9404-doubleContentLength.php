<?php

$clength = filesize(__FILE__);

header("Content-length: $clength");
header("Content-length: $clength", false);

readfile(__FILE__);
