<?php

namespace Balsama\BostonPlateLookup;

use DOMDocument;

class ResponseParser
{

    public static function isFound($response): bool
    {
        if (str_contains($response, 'Information not found.')) {
            return false;
        }
        if (str_contains($response, 'Plate Number not found')) {
            return false;
        }
        return true;
    }

    public static function getBalance($response): float
    {
        if (!str_contains($response, 'The Plate entered has a balance of')) {
            if (str_contains($response, 'The amount of the plate entered is')) {
                $balance = self::getStringBetween($response, '<p>The amount of the plate entered is: &nbsp;&nbsp;&nbsp;<strong>', '</strong>');
            }
            else {
                throw new \Exception('Expected to find balance in output but did not.');
            }
        }
        else {
            $balance = self::getStringBetween($response, 'The Plate entered has a balance of ', '</li></ul>');
        }
        $balance = ltrim($balance, '$');
        return (float) $balance;
    }

    public static function getTickets($response)
    {
        $table = self::getStringBetween($response, 'Please select the items you wish to pay:</p>', '<br>');

        $dom = new domDocument;
        @$dom->loadHTML($table);
        $dom->preserveWhiteSpace = false;
        $tables = $dom->getElementsByTagName('table');

        $rows = $tables->item(0)->getElementsByTagName('tr');
        $rowData = [];
        $i = 0;
        foreach ($rows as $row) {
            $cols = $row->getElementsByTagName('td');
            foreach ($cols as $col) {
                $rowData[$i][] = $col->nodeValue;
            }
            $i++;
        }

        $tickets = [];
        foreach ($rowData as $possibleTicket) {

            if (count($possibleTicket) === 7) {
                $tickets[] = new Ticket(
                    '',
                    $possibleTicket[1],
                    $possibleTicket[2],
                    $possibleTicket[3],
                    $possibleTicket[4],
                    $possibleTicket[5],
                    ltrim($possibleTicket[6], '$'),
                );
            }
        }

        return $tickets;
    }

    private static function getStringBetween($string, $start, $end): string
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

}