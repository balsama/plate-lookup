<?php

include_once('vendor/autoload.php');

use Balsama\BostonPlateLookup\Lookup;
use Balsama\BostonPlateLookup\Helpers;

$plateNumber = $argv[1];
if (!is_string($plateNumber)) {
    throw new Exception('You must provide a plate number as an argument to this script');
}

$database = \Balsama\BostonPlateLookup\Helpers::initializeDatabase();

$existingRecord = $database->select('lookup', ['plate_number', 'fetched_timestamp'], [
    'plate_number' => $plateNumber,
    'ORDER' => ['fetched_timestamp' => 'DESC'],
    'LIMIT' => 1,
]);

if (!$existingRecord) {
    $lookup = new Lookup($plateNumber);
    $lookup->saveToDb();
} else {
    $existingRecordTimestamp = reset($existingRecord)['fetched_timestamp'];
    if ((time() - $existingRecordTimestamp) > 0) {

        $existingRecordBirthday = $database->select(
            'birthdays',
            ['birth_month', 'birth_monthday'],
            ['plate_number' => $plateNumber]
        );

        if (!$existingRecordBirthday) {
            $lookup = new Lookup($plateNumber);
        } else {
            $existingRecordBirthday = reset($existingRecordBirthday);
            $yearDay = Helpers::getYearDayFromMonthAndMonthday(
                $existingRecordBirthday['birth_month'],
                $existingRecordBirthday['birth_monthday']
            );
            $lookup = new Lookup($plateNumber, 'PA', $yearDay);
        }
        $lookup->saveToDb();
    }
}

$record = $database->select('lookup', '*', [
    'plate_number' => $plateNumber,
    'ORDER' => ['fetched_timestamp' => 'DESC'],
    'LIMIT' => 1,
]);
$record = reset($record);
$tickets = $database->select('tickets', '*', [
    'plate_number' => $plateNumber,
]);

$format = "Plate %s has a current balance of $%4.2f.\n";
print sprintf($format, $plateNumber, $record['balance']);
if ($tickets) {
    print "Tickets:\n";
    foreach ($tickets as $ticket) {
        /* @var \Balsama\BostonPlateLookup\Ticket $ticket */
        $format = "%s issued %s %s at %s.\n";
        print sprintf($format, $ticket['infraction'], $ticket['infraction_date'], $ticket['infraction_time'], $ticket['infraction_address']);
    }
}