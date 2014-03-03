<?php

namespace Zend\Filter\File;

function move_uploaded_file($source, $dest)
{
    return rename($source, $dest);
}
