<?php

namespace Balsama\BostonPlateLookup;

use Medoo\Medoo;

class Helpers
{
    public static function initializeDatabase(): Medoo
    {
        if (getenv('LOOKUPENV') === 'dioc') {
            $database = new Medoo([
                'type' => 'pgsql',
                'host' => getenv('DIOC_PG_HOST'),
                'port' => getenv('DIOC_PG_port'),
                'database' => getenv('DIOC_PG_DB_NAME'),
                'username' => getenv('DIOC_PG_DB_USERNAME'),
                'password' => getenv('DIOC_PG_PW'),
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

        return $database;
    }

    public static function getYearDayFromMonthAndMonthday(int $month, int $monthday): int
    {
        $timestamp = strtotime("$month/$monthday");
        $yearDay = date('z', $timestamp);
        return ($yearDay + 1);
    }
}