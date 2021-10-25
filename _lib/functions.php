<?php

function fileVersion($file, $return = false)
{
    if (file_exists(DJC_PATH . $file))
    {
        $mtime = filemtime(DJC_PATH . $file);
        $file  = DJC_URL . $file . "?" . $mtime;
    }
    else
    {
        if ($return == true)
            $file = "";
    }

    if ($return == false)
        echo $file;
    else
        return $file;
}
