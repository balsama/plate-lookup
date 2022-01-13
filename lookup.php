<?php

include_once('vendor/autoload.php');

use Balsama\BostonPlateLookup\Lookup;
use Balsama\BostonPlateLookup\Helpers;

if (!is_string($_POST['plate_number'])) {
    throw new Exception('You must provide a plate number as an argument to this script');
}
$plateNumber = strtolower(trim($_POST['plate_number']));
if (strlen($plateNumber) > 10) {
    throw new Exception('Plate number cannot be longer than ten characters');
}

$database = Helpers::initializeDatabase();

$existingRecord = $database->select('lookup', ['plate_number', 'fetched_timestamp'], [
    'plate_number' => $plateNumber,
    'ORDER' => ['fetched_timestamp' => 'DESC'],
    'LIMIT' => 1,
]);

if (!$existingRecord) {
    $lookup = new Lookup($plateNumber);
    $lookup->saveToDb();
}
else {
    $existingRecordTimestamp = reset($existingRecord)['fetched_timestamp'];
    if ((time() - $existingRecordTimestamp) > 86400) {

        $existingRecordBirthday = $database->select(
            'birthdays',
            ['birth_month', 'birth_monthday'],
            ['plate_number' => $plateNumber]
        );

        if (!$existingRecordBirthday) {
            $lookup = new Lookup($plateNumber);
        }
        else {
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
unset($_SERVER);
unset($_COOKIE);
unset($_GET);
unset($_REQUEST);
$tickets = $database->select('tickets', '*', [
    'plate_number' => $plateNumber,
]);

$message = '';
if ($record['found']) {
    $format = "Plate \"%s\" has a current balance of $%4.2f.\n";
    $message = sprintf($format, strtoupper($plateNumber), $record['balance']);
}
else {
    $format = "Unable to find plate \"%s\" in the system.\n";
    $message = sprintf($format, strtoupper($plateNumber));
}
if ($tickets) {
    $message .= "\n" . count($tickets) . " total Tickets found:\n";
    foreach ($tickets as $ticket) {
        /* @var \Balsama\BostonPlateLookup\Ticket $ticket */
        $format = "• %s: $%4.2f - issued %s %s at %s.\n";
        $message .= sprintf($format, $ticket['infraction'], $ticket['fine'], $ticket['infraction_date'], $ticket['infraction_time'], $ticket['infraction_address']);
    }
}

print $message;
