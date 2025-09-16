<pre>
<?php
//      https://css-tricks.com/indicating-scroll-position-on-a-page-with-css/
//      https://www.w3schools.com/howto/howto_js_scroll_indicator.asp

// if ($_SERVER['REMOTE_ADDR'] == $_ENV['HOME_IP']) {
//     error_reporting(E_ALL); // Report all errors
//     ini_set('display_errors', 1); // See errors' stack trace
// //    phpinfo();
// }

$title = 'GameStop Short FTD Squeeze Timeline';
$url = 'gmetimeline.com';

require_once('../php/Event.class.php');
//require_once('../php/Events.php');
require_once('../php/Views.php');

//global $Events;

// Uncomment this line to export php/Events.php into JSON
//echo json_encode($Events, JSON_PRETTY_PRINT);

$dir    = 'events';
$files = scandir($dir);
$Events = [];
foreach($files as $file) {
    // Skip this file if it isn't a JSON file
    if (pathinfo($file, PATHINFO_EXTENSION) !== 'json') continue;
//echo $file;
    $Events = array_merge($Events, json_decode(urldecode(file_get_contents("$dir/$file")), true));
//    break;
}
print_r($Events);
?>
</pre>
