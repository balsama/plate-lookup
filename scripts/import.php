<?php

include_once('vendor/autoload.php');

use Balsama\BostonPlateLookup\Ticket;
use Balsama\BostonPlateLookup\SaveToDb;
use Balsama\BostonPlateLookup\Helpers;

$csv = file('data/parking_tickets.csv');
Helpers::initializeDatabase();

$data = [];
foreach ($csv as $line) {
    $data[] = str_getcsv($line);
}

array_shift($data);

foreach ($data as $rawTicket) {
    $ticket = new Ticket(
        strtolower($rawTicket[12]),
        $rawTicket[0],
        $rawTicket[1],
        $rawTicket[2],
        $rawTicket[4],
        $rawTicket[6],
        (float) ltrim($rawTicket[14], '$'),
    );

    SaveToDb::insertTicket($ticket);
}

$foo = 21;