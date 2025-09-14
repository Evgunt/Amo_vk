<?php
function Write($filename, $text) // Логгирование
{
    $file = "logs/$filename.txt";

    if ((int)filesize($file) > 50000)
        file_put_contents($file, '');
    file_put_contents($file, sprintf(
        '%s%s========================================================================================================================%s',
        print_r([
            "data" => $text,
            "time" => date('d.m.Y H:i:s')
        ], true),
        PHP_EOL . PHP_EOL,
        PHP_EOL . PHP_EOL
    ), FILE_APPEND);
}
