<?php

namespace Balsama\BostonPlateLookup;

class PlateInfo
{
    private bool $isFound = false;
    private int $birthMonth;
    private int $birthDay;
    private string $fullResponse;
    private float $balance;
    private array $tickets = [];
    private string $plateType;
    private string $vehicleMake;

    public function __construct(
        public string $plateNumber,
    )
    {}

    public function setIsFound($isFound)
    {
        $this->isFound = true;
    }
    public function setBirthday(int $month, int $day)
    {
        $this->birthMonth = $month;
        $this->birthDay = $day;
    }

    public function setFullResponse($fullResponse)
    {
        $this->fullResponse = $fullResponse;
    }
    public function setBalance(float $balance)
    {
        $this->balance = $balance;
    }
    public function setPlateType(string $type)
    {
        $this->plateType = $type;
    }
    public function setvVehicleMake(string $vehicleMake)
    {
        $this->vehicleMake = $vehicleMake;
    }

    public function addTicket(Ticket $ticket)
    {
        $this->tickets[] = $ticket;
    }

    public function getPlateNumber(): string
    {
        return $this->plateNumber;
    }
    public function getIsFound(): bool
    {
        return $this->isFound;
    }
    public function getBirthday(): string
    {
        return "$this->birthMonth/$this->birthDay";
    }
    public function getBirthMonth(): int
    {
        return $this->birthMonth;
    }
    public function getBirthMonthDay(): int
    {
        return $this->birthDay;
    }
    public function getFullResponse(): string
    {
        return $this->fullResponse;
    }
    public function getBalance(): float
    {
        return $this->balance;
    }
    public function getTickets()
    {
        return $this->tickets;
    }
    public function getPlateType()
    {
        return $this->plateType;
    }
    public function getVehicleMake()
    {
        return $this->vehicleMake;
    }
}