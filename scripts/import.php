<?php

include_once('vendor/autoload.php');

use Balsama\BostonPlateLookup\Ticket;
use Balsama\BostonPlateLookup\SaveToDb;
use Balsama\BostonPlateLookup\Helpers;
use Balsama\BostonPlateLookup\PlateInfo;

$csv = file('data/parking_tickets.csv');
Helpers::initializeDatabase();

$data = [];
foreach ($csv as $line) {
    $data[] = str_getcsv($line);
}

array_shift($data);

$i = 0;
foreach ($data as $rawTicket) {
    $ticket = new Ticket(
        strtolower($rawTicket[12]),
        $rawTicket[11],
        $rawTicket[0],
        $rawTicket[1],
        $rawTicket[2],
        $rawTicket[4],
        $rawTicket[6],
        (float) ltrim($rawTicket[14], '$'),
    );

    $plateInfo = new PlateInfo($rawTicket[12]);
    $plateInfo->setVehicleMake($rawTicket[10]);

    SaveToDb::insertTicket($ticket, $plateInfo);
    $i++;
    if ($i % 1000 == 0) {
        print "Imported $i records ...\n";
    }
}
print "Imported all $i records. \n";