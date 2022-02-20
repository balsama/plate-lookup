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

    public static function insertTicket($ticket, PlateInfo $plateInfo)
    {
        $database = Helpers::initializeDatabase();

        self::insertPlate($database, $ticket, $plateInfo);

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

    public static function insertPlate(Medoo $database, Ticket $ticket, PlateInfo $plateInfo)
    {
        $existingRecord = $database->select('plates', ['plate_number', 'vehicle_make'], [
            'plate_number' => $ticket->plateNumber,
        ]);

        if ($existingRecord) {
            if ($plateInfo->getVehicleMake() == 'UKN') {
                // There's already a record, and this record doesn't know the vehicle type, so exit.
                return;
            }
            if ($existingRecord[0]['vehicle_make'] !== 'UKN') {
                // There is already a vehicle make stored for this plate.
                return;
            }
            $database->delete('plates', ['plate_number' => $ticket->plateNumber]);
        }

        $database->insert(
            'plates',
            [
                'plate_number' => $ticket->plateNumber,
                'plate_type' => $ticket->plateType,
                'vehicle_make' => $plateInfo->getVehicleMake(),
            ]
        );
    }

    public function insertTickets()
    {
        if ($tickets = $this->record->getTickets()) {
            /* @var Ticket[] $tickets */
            foreach ($tickets as $ticket) {
                self::insertTicket($ticket, $this->record);
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
