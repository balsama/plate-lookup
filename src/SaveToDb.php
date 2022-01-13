<?php

namespace Balsama\BostonPlateLookup;

use Medoo\Medoo;

class SaveToDb
{
    private Medoo $database;
    private PlateInfo $record;
    private int $timestamp;

    public function __construct(PlateInfo $record)
    {
        $this->timestamp = time();
        $this->database = Helpers::initializeDatabase();

        $this->record = $record;

        $this->insertBirthday();
        $this->insertLookup();
        $this->insertTickets();
    }

    public function insertBirthday()
    {
        if ($this->record->getIsFound()) {
            $existingRecord = $this->database->select('birthdays', 'plate_number', [
                'plate_number' => $this->record->getPlateNumber(),
            ]);
            if ($existingRecord) {
                return;
            }

            $this->database->insert(
                'birthdays',
                [
                    'plate_number' => $this->record->plateNumber,
                    'birth_month' => $this->record->getBirthMonth(),
                    'birth_monthday' => $this->record->getBirthMonthDay(),
                ]);
        }
    }

    public function insertLookup()
    {
        if ($this->record->getIsFound()) {
            return $this->insertFoundRecord();
        }
        else {
            return $this->insertNotFoundRecord();
        }
    }

    public static function insertTicket($ticket)
    {
        $database = Helpers::initializeDatabase();
        $existingRecord = $database->select('tickets', 'ticket_number', [
            'ticket_number' => $ticket->ticketNumber,
        ]);

        if ($existingRecord) {
            return;
        }

        $database->insert(
            'tickets',
            [
                'ticket_number' => $ticket->ticketNumber,
                'plate_number' => $ticket->plateNumber,
                'infraction' => $ticket->reason,
                'fine' => $ticket->amount,
                'infraction_date' => $ticket->dateIssuedString,
                'infraction_time' => $ticket->timeIssuedString,
                'infraction_address' => $ticket->address,
            ]
        );
    }

    public function insertTickets()
    {
        if ($tickets = $this->record->getTickets()) {
            /* @var Ticket[] $tickets */
            foreach ($tickets as $ticket) {
                self::insertTicket($ticket);
            }
        }
    }

    public function insertFoundRecord(): ?\PDOStatement
    {
        return $this->database->insert(
            'lookup',
            [
                'plate_number' => $this->record->getPlateNumber(),
                'found' => $this->record->getIsFound(),
                'balance' => $this->record->getBalance(),
                'full_response' => $this->record->getFullResponse(),
                'fetched_timestamp' => $this->timestamp,
            ]
        );
    }
    public function insertNotFoundRecord(): ?\PDOStatement
    {
        return $this->database->insert(
            'lookup',
            [
                'plate_number' => $this->record->getPlateNumber(),
                'found' => 0,
                'fetched_timestamp' => $this->timestamp,
            ]
        );
    }
}
