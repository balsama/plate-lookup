<?php

namespace Balsama;

use Balsama\BostonPlateLookup\Helpers;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{

    public function testProcessPlate()
    {
        $notFounfPlateNumber = 'zzzzzz';
        $uuid = '123abc';
        $response = Helpers::processPlate($notFounfPlateNumber, $uuid);
        $this->assertIsObject($response);
        $this->assertTrue($response->found);
        $this->assertObjectHasAttribute('vehicle_make', $response);

        $plateNumber = '3pr517';
        $uuid = '123abc';
        Helpers::processPlate($plateNumber, $uuid);

        $plateNumber = '6dx325';
        $uuid = '123abc';
        Helpers::processPlate($plateNumber, $uuid);
    }

}