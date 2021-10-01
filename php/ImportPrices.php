<?php

require_once('Event.class.php');

$file   = $_ENV['EVENTS'] . '/GME.csv';
$Prices = [];

// Open GME.csv which contains historical price data
if (($fh = @fopen($file, 'r')) !== false) {
    // First CSV line contains headers which we will use later as keys in $key => data arrays
    $header = array_map("strtolower", fgetcsv($fh));

    // Iterate through each CSV line, 1 line = 1 date
    while ($row = fgetcsv($fh)) {
        // Create new array where keys are from $header and $row is that date's value
        $Prices[$row[0]] = array_combine($header, $row);

        // Unset a couple of keys because their values don't exist in the GME\Event object and will cause problems later
        unset($Prices[$row[0]]['date'], $Prices[$row[0]]['adj close']);
    }
}
//print_r($Prices);