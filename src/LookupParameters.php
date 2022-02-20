<?php

namespace Balsama\BostonPlateLookup;

class LookupParameters
{
    public function __construct(
        public string $plateNumber,
        public string $plateType,
        public int $yearDay = 1,
    )
    {}

    public function incrementYearDay()
    {
        $this->yearDay = ($this->yearDay + 1);
    }

    public function getMonth(): string
    {
        $timestamp = strtotime("January 1st +".($this->yearDay - 1)." days");
        $month = date('m', $timestamp);
        return (string) $month;
    }

    public function getMonthDay(): string
    {
        $timestamp = strtotime("January 1st +".($this->yearDay - 1)." days");
        $dayOfMonth = date('d', $timestamp);
        return (string) $dayOfMonth;
    }

    public function getPlateType(): string
    {
        return $this->plateType;
    }
}