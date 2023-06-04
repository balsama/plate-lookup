<?php

include_once('vendor/autoload.php');

use Balsama\BostonPlateLookup\Ticket;
use Balsama\BostonPlateLookup\SaveToDb;
use Balsama\BostonPlateLookup\Helpers;
use Balsama\BostonPlateLookup\PlateInfo;

$importDirectory = __DIR__ . '/../data/2023/';
$importFiles = array_diff(scandir($importDirectory), ['..', '.']);

foreach ($importFiles as $file) {

    print date("G:i:s", time()) . " Starting file $file.\n";
    $csv = file($importDirectory . $file);
    Helpers::initializeDatabase();

    $i = 0;
    foreach ($csv as $line) {
        $rawTicket = str_getcsv($line);

        $ticket = new Ticket(
            strtolower($rawTicket[14]), // Plate number
            $rawTicket[12], // Plate type
            $rawTicket[0], // Ticket Number
            $rawTicket[1], // Date issued
            $rawTicket[2], // Time issued
            $rawTicket[5], // Reason
            $rawTicket[7], // Address
            (float)ltrim($rawTicket[15], '$'), // Amount
        );

        $plateInfo = new PlateInfo($rawTicket[14]);
        $plateInfo->setVehicleMake($rawTicket[11]);

        SaveToDb::insertKnownUniqueTicket($ticket, $plateInfo);
        $i++;
        if ($i % 1000 == 0) {
            print date("G:i:s", time()) . " Imported $i records ...\n";
        }
    }
    unset($csv);
    print date("G:i:s", time()) . " Imported all $i records from $file.\n";
}