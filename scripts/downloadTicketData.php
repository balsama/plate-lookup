<?php
include_once('vendor/autoload.php');

use Balsama\BostonPlateLookup\Helpers;

$filename = 'data/parking_tickets.csv';
file_put_contents("$filename.gz", fopen("https://wokewindows-data.s3.amazonaws.com/parking_tickets.csv.gz", 'r'));

//This input should be from somewhere else, hard-coded in this example
$sourceFileName = 'data/parking_tickets.csv.gz';

// Raising this value may increase performance
$buffer_size = 4096; // read 4kb at a time
$outputFileName = 'data/parking_tickets.csv';

// Open our files (in binary mode)
$file = gzopen($sourceFileName, 'rb');
$out_file = fopen($outputFileName, 'wb');

// Keep repeating until the end of the input file
while (!gzeof($file)) {
    // Read buffer-size bytes
    // Both fwrite and gzread and binary-safe
    fwrite($out_file, gzread($file, $buffer_size));
}

// Files are done, close files
fclose($out_file);
gzclose($file);