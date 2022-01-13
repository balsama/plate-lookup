<?php

namespace Balsama\BostonPlateLookup;

use Medoo\Medoo;

class Helpers
{
    public static function initializeDatabase(): Medoo
    {
        if (getenv('LOOKUPENV') === 'dioc') {
            $database = new Medoo([
                'type' => 'postgresql',
                'host' => 'app-42d2b891-6867-4baa-98c5-c4ba9a24675f-do-user-2963441-0.b.db.ondigitalocean.com',
                'port' => 25060,
                'database' => 'lookups.db',
                'username' => 'lookups',
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
            'id' => [
                'INTEGER',
                'PRIMARY KEY'
            ],
            'plate_number' => ['TEXT'],
            'found' => ['INTEGER'],
            'balance' => ['FLOAT'],
            'full_response' => ['TEXT'],
            'fetched_timestamp' => ['INTEGER'],
        ]);

        $database->create('tickets', [
            'id' => [
                'INTEGER',
                'PRIMARY KEY',
            ],
            'ticket_number' => ['TEXT', 'UNIQUE'],
            'plate_number' => ['TEXT'],
            'infraction' => ['TEXT'],
            'fine' => ['FLOAT'],
            'infraction_date' => ['TEXT'],
            'infraction_time' => ['TEXT'],
            'infraction_address' => ['TEXT'],
        ]);

        $database->create('birthdays', [
            'id' => [
                'INTEGER',
                'PRIMARY KEY'
            ],
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