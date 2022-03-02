<?php

namespace Balsama;

use Balsama\BostonPlateLookup\ResponseParser;
use PHPUnit\Framework\TestCase;

class ResponseParserTest extends TestCase
{

    public function testGetBalance()
    {
        $response = file_get_contents(__DIR__ . '/../../example-responses/response-found--no-balance.html');
        $balance = ResponseParser::getBalance($response);
        $this->assertEquals(0.00, $balance);

        $response = file_get_contents(__DIR__ . '/../../example-responses/response-found--balance--multiple-tickets.html');
        $balance = ResponseParser::getBalance($response);
        $this->assertEquals(106.00, $balance);
    }

}