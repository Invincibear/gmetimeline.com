<?php
$maxTipsPerDay = 3;
$tips = [];
$tipsFile = '../tips.csv';
$submittedTips = explode("\n", file_get_contents('../tips.csv'));


foreach ($submittedTips as $fileTip) {
    if (empty($fileTip)) continue;

    list($ip, $timestamp) = explode(',', $fileTip);
    $tips[$ip][] = $timestamp;
}

// This user has previously submitted a tip, begin rate-limiting verifications
if (array_search($_SERVER['REMOTE_ADDR'], array_keys($tips)) !== false) {
    $timestamps = $tips[$_SERVER['REMOTE_ADDR']];
    $count = count($timestamps);

    // Looks like the user has submitted at least $maxTipsPerDay tips total
    // Let's inspect the $tips[$_SERVER['REMOTE_ADDR']][$count-$maxTipsPerDay] tip to see if it was within 24hrs
    if ($count >= $maxTipsPerDay) {
        sort($timestamps);
        $key = array_key_last($timestamps);
        $maxTipAgo = $tips[$_SERVER['REMOTE_ADDR']][$count-$maxTipsPerDay];

        // User has maxed-out their tip submission, return 429 error code
        if ((time() - $maxTipAgo) <= (60 * 60 * 24)) {
            // Add new attempt to CSV
            try {
                $fp = @fopen($tipsFile, 'a');
                @fwrite($fp, "\n{$_SERVER['REMOTE_ADDR']}," . time());
                @fclose($fp);
            } catch (Exception $e) {
            }

            // Now shut them down
            // https://en.wikipedia.org/wiki/List_of_HTTP_status_codes#4xx_client_errors
            header("HTTP/1.0 429 Too Many Requests");
            exit;
        }
    }
}

// Add new attempt to CSV
try {
    $fp = @fopen($tipsFile, 'a');
    @fwrite($fp, "\n{$_SERVER['REMOTE_ADDR']}," . time());
    @fclose($fp);
} catch (Exception $e) {
    // Don't let the user continue if our script is broken
    // Or maybe let them, incoming spam will let me know this is broken LOL
//    exit;
}

// Sanitize & filter the incoming tip
$tip = trim(html_entity_decode(filter_var($_POST['tip'], FILTER_SANITIZE_STRING)));

// If the tip is empty
if (empty($tip)) {
    header("HTTP/1.0 400 Bad Request");
    exit;
}

// Otherwise everything looks good, try to send me the tip via email
try {
    @mail($_ENV['EMAIL'], "New Tip From gmetimeline.com", $tip, "From:tips@gmetimeline.com");
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    exit;
}

header("HTTP/1.0 200 OK");
