<?php

namespace Balsama\BostonPlateLookup;

use Medoo\Medoo;
use Ramsey\Uuid\Uuid;
use PDO;

class Helpers
{
    public static function initializeDatabase(): Medoo
    {
        if (file_exists(__DIR__ . '/../../connections/diocdb.json')) {
            $connection = json_decode(file_get_contents(__DIR__ . '/../../connections/diocdb.json'));

            $dsnVars = [
                'dbname' => $connection->database,
                'host' => $connection->host,
                'sslmode' => 'require',
                'port' => $connection->port,
            ];

            foreach($dsnVars as $id => $value) {
                $pair[] = implode('=', [$id, $value]);
            }
            $dsn = 'pgsql:' . implode(';', $pair);

            $pdo = new PDO($dsn, $connection->username, $connection->password);
            $database = new Medoo([
                'pdo' => $pdo,
                'type' => 'pgsql'
            ]);
        }
        else {
            $database = new Medoo([
                'type' => 'sqlite',
                'database' => 'lookups.db'
            ]);
        }

        $database->create('lookup', [
            'plate_number' => ['TEXT'],
            'found' => ['INTEGER'],
            'balance' => ['FLOAT'],
            'full_response' => ['TEXT'],
            'fetched_timestamp' => ['INTEGER'],
        ]);

        $database->create('tickets', [
            'ticket_number' => ['TEXT'],
            'plate_number' => ['TEXT'],
            'infraction' => ['TEXT'],
            'fine' => ['FLOAT'],
            'infraction_date' => ['TEXT'],
            'infraction_time' => ['TEXT'],
            'infraction_address' => ['TEXT'],
        ]);

        $database->create('birthdays', [
            'plate_number' => ['TEXT'],
            'birth_month' => ['INTEGER'],
            'birth_monthday' => ['INTEGER'],
        ]);

        $database->create('plates', [
            'plate_number' => ['TEXT'],
            'plate_type' => ['TEXT'],
            'vehicle_make' => ['TEXT'],
        ]);

        return $database;
    }

    public static function getYearDayFromMonthAndMonthday(int $month, int $monthday): int
    {
        $timestamp = strtotime("$month/$monthday");
        $yearDay = date('z', $timestamp);
        return ($yearDay + 1);
    }

    public static function processPlate(string $plateNumber, string $uuid)
    {
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

        $tickets = $database->select('tickets', '*', [
            'plate_number' => $plateNumber,
        ]);

        $message = '';
        if ($record['found']) {
            $format = "Plate \"%s\" has a current balance of $%4.2f.\n";
            $message = sprintf($format, strtoupper($plateNumber), $record['balance']);
        }
        else {
            $format = "Unable to find current balance for plate \"%s\" in the system.\n";
            $message = sprintf($format, strtoupper($plateNumber));
        }
        if ($tickets) {
            $message .= "\n" . count($tickets) . " total Tickets found:\n";
            foreach ($tickets as $ticket) {
                /* @var \Balsama\BostonPlateLookup\Ticket $ticket */
                $format = " - %s: $%4.2f issued %s %s at %s.\n";
                $message .= sprintf($format, $ticket['infraction'], $ticket['fine'], $ticket['infraction_date'], $ticket['infraction_time'], $ticket['infraction_address']);
            }
        }


        if (file_put_contents('results/' . $uuid . '.txt', $message)) {
            return [
                'message' => $message,
                'total_tickets' => count($tickets),
            ];
        }

    }
}