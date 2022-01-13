<?php

include_once('vendor/autoload.php');

use Balsama\BostonPlateLookup\Lookup;

$plates =[
    '1krk97',
    '1wma43',
    '1mwl12',
    'y212',
    'evl273',
    'y212',
    'evl273',
    'q226',
    '2jc569',
    'fitzme',
    'g2456',
    '1wfy35',
    '8vt291',
];

foreach ($plates as $plate) {
    $lookup = new Lookup($plate);
    $lookup->saveToDb();
}
