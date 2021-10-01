<?php

require_once('Event.class.php');
global $Prices;
use GME\Event;

$dir          = $_ENV['EVENTS'];
$files        = scandir($dir);
$LastModified = [];
$EventsJSON   = [];
$Events       = [];

// Get raw events data from .json files in events folder
foreach($files as $file) {
    // Skip this file if it isn't a JSON file
    if (pathinfo($file, PATHINFO_EXTENSION) !== 'json') continue;

    // Add the file's last modified date to an array, account for our -8PST timezone
    $LastModified[] = filemtime("$dir/$file") - (60 * 60 * 8);

    // Add each JSON file's contents to the master $Events
    // Convert from JSON -> Array
    $newEvents = json_decode(file_get_contents("$dir/$file"), true);
    if ($newEvents === NULL) continue; // Prevents catastrophic failure of events rendering

    $EventsJSON = array_merge($EventsJSON, $newEvents);
}

// Sort by most recent timestamp so that we can access it via [0] in index.php for left/right timeline placement
rsort($LastModified);

// Iterate through each date, then iterate through each event to convert it to an object
foreach ($EventsJSON as $date => $events) {
    $Events[$date] = [];

    foreach ($events as $event) {
        // First, inject historical price data into the event (only if we have data for that date)
        $merged = (!empty($Prices[$date])) ? array_merge($event, $Prices[$date]) : $event;

        // Convert from Array -> Object
        $Events[$date][] = new Event(...$merged);
    }
}

// We now want to show the newest events first, so reverse the array order
$EventsAsc = $Events;
$Events = array_reverse($Events);