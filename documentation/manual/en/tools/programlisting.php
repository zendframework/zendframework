<?php
$dir = new DirectoryIterator('../module_specs/');

function InsertSpace($matches)
{
    return $matches[1].$matches[2].$matches[3].str_pad(' ',strlen($matches[1])).$matches[4];
}

foreach ($dir as $file)
{
    if ($file->isFile()) {
        $text = file_get_contents($file->getPathName());
        // Put \n after CDATA if none
        $text = preg_replace('/(\<\!\[CDATA\[)([^\n])/', "$1\n$2", $text);
        // Omit space (not \n) before ]]>
        $text = preg_replace('/([^\n])(\s*)(\]\]\>\<\/programlisting\>)/', "$1\n$3", $text);
        // Reset all space between ]]> and </programlisting>
        $text = preg_replace('/(\]\]\>)(\n|\s)*(\<\/programlisting\>)/', "$1$3", $text);
        // Omit last ? > of the programlisting
        $text = preg_replace('/(\?\>)(\n|\s)*(\]\]\>)/', "$2$3", $text);
        // Put \n before ]]> if none
        $text = preg_replace('/([^\n])(\]\]\>)(\<\/programlisting\>)/', "$1\n$2$3", $text);
        // Put \n between ]]> and </programlisting>
        $text = preg_replace('/(\]\]\>)(\<\/programlisting\>)/', "$1\n$2", $text);
        // Put same indent before </programlisting> as before <programlisting>
        $text = preg_replace_callback('/([^\n]*)(<programlisting role="php"><)(.*?)(<\/programlisting>)/s', "InsertSpace", $text);
        file_put_contents($file->getPathName(),$text);
    }
}