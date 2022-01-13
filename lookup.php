<?php

include_once('vendor/autoload.php');

use Balsama\BostonPlateLookup\Helpers;
use Ramsey\Uuid\Uuid;

set_time_limit(0);
ob_start();

if (!$_POST) {
    echo '200 ok<p>POST a <code>plate_number</code> value to look up tickets.</p>';
    exit;
}
if (!is_string($_POST['plate_number'])) {
    throw new Exception('You must provide a plate number as an argument to this script');
}
$plateNumber = strtolower(trim($_POST['plate_number']));
if (strlen($plateNumber) > 10) {
    throw new Exception('Plate number cannot be longer than ten characters');
}

$uuid = Uuid::uuid4();
print 'Your custom endpoint is: ' . $uuid->toString() . '.txt';

header('Connection: close');
header('Content-Length: '.ob_get_length());
ob_end_flush();
ob_flush();
flush();

Helpers::processPlate($plateNumber, $uuid);

die();
