<?php

namespace Balsama\BostonPlateLookup;

class Ticket
{
    public function __construct(
        public string $plateNumber,
        public string $ticketNumber,
        public string $dateIssuedString,
        public string $timeIssuedString,
        public string $reason,
        public string $address,
        public float $amount,
    )
    {}
}