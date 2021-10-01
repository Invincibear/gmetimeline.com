<?php

function FormatYears(array $Events): string {
    $year = '';
    $output = '';

    foreach ($Events as $date => $events) {
        $yearstamp = '<option value="%1$s">%1$s</option>';

        if ($date === array_key_first($Events)) {
            $year = substr($date, 0, 4);
            $output .= sprintf($yearstamp, $year);
        }

        // The year has changed, we insert the new year into the timeline
        if ($year != substr($date, 0, 4)) {
            $year = substr($date, 0, 4);
            $output .= sprintf("\n\t\t\t" . $yearstamp, $year);
        }
    }

    return $output;
}


// Replaces images with linked images so users can view the full size of the original image
function InjectImageLinks(string $description): string {
    return preg_replace(
        '/<img src="(.+)"(.*)>/Uis',
        '<a href="$1" target="_blank"><img src="$1"$2></a>',
        $description
    );
}


// Output a bunch of blank timeline events
function FormatBlankEvents(int $count = 10): string {
    global $Prices;

    $left = true;
    $output = '';
    $year = '';
    $first = ' first';

    for ($i = 0; $i < $count; $i++) {
        $date = date(DATE_ISO8601, time() - ($i * 60 * 60 * 24));
        $events = range($i, $i);
// as $date => $events
        $prevDay = date("Y-m-d", prevWeekday(strtotime($date)));

        $yearstamp =
            "<section>\n\t\t\t" .
            '<div class="newYear%2$s">' .
            "\n\t\t\t\t" .
            '<a onClick="toggleYear(%1$d)" onmouseover="" class="hint--bottom-right" data-tippy-placement="bottom-start" data-tippy-content="Click to hide/show all %1$d timeline events" aria-label="Click to hide/show all %1$d timeline events"><time datetime="%1$d" id="y%1$d">%1$d</time></a>' .
            "\n\t\t\t</div><br>"; //hint--bottom-right

        if ($i === 0) {
            // First event, so we must first display the year
            $year = substr($date, 0, 4);
            $output .= sprintf($yearstamp, $year, $first);
            $first = '';
        }

        // Check if all events within this date are hidden. If they are, do not bit flip $side
        $allHidden = true;
        $dateStamped = false;
//        while ($allHidden === false) {
//            if ($event['hidden']) $allHidden = true;
//        }
        $side = ($left) ? 'left' : 'right';

        // The year has changed, we insert the new year into the timeline
        if ($year != substr($date, 0, 4)) {
            $year = substr($date, 0, 4);
            $output .= sprintf("\n\t\t</section>\n\t\t" . $yearstamp, $year, $first);
        }

        foreach ($events as $j => $event) {
            $allHidden = false;

            $shareId = '';
            $output .= sprintf(
                "\n\t\t\t" . '<article class="container %1$s %2$d" data-year="%2$d" data-month="%3$d" data-day="%4$d" data-tags="%5$s" data-key="%6$d" id="%7$s">',
                $side,
                $year,
                substr($date, 5, 2),
                substr($date, 8),
                '',
                $i,
                $shareId
            );

            // Show the datestamp for only the first card of a date
//            $hidden = (array_key_first($events) === $i) ? '' : ' hidden';
            $hidden = (!$dateStamped) ? '' : ' hidden';
            $output .= sprintf("\n\t\t\t" . '<time datetime="%s" class="eventDate%s">%s</time>', $date, $hidden, date("M j", strtotime($date)));
            $icon = '';//'fas fa-power-off';
            $prevClose = null;
            $prices = '';

            $output .= sprintf(
                (
                    "\n\t\t\t\t" . '<i class="icon %s"></i>' .
                    "\n\t\t\t\t" . '<main class="content%s">' .
                    "\n\t\t\t\t\t<section>" .
//                    "\n\t\t\t\t\t\t" . '<a onclick="shareTimelineEvent(\'%s\');" class="hint--right share" aria-label="Click to share this timeline event"><i class="share fas fa-share-alt"></i></a>' .
                    "\n\t\t\t\t\t\t<h2>%s</h2>" .
                    "\n\t\t\t\t\t\t<p>%s</p>" .
                    "\n\t\t\t\t\t</section>\n\t\t\t\t\t<footer>" .
                    "%s\n\t\t\t\t\t\t<q>%s</q>\n\t\t\t\t\t\t" . '<span class="tags">%s</span>' .
                    "\n\t\t\t\t\t</footer>\n\t\t\t\t</main>\n\t\t\t<!-- %s --></article>"
                ),
                $icon,
                '',
//                $shareId,
                '',
                '<br><br><br><br>',
                '',
                $prices,
                '',
                '',
            );

            // We have displayed the datestamp once for this date, bit flip so we don't display it again
            $dateStamped = true;
        }

        // Only bit flip the next timeline event's side of the vertical timeline divider if not all events of a date are hidden
        $left = ($allHidden) ? $left : !$left;
    }

    return $output;
}

// This format outputs one event per timeline card
function FormatEvents(array $Events): string {
    global $Prices;

    $left = true;
    $output = '';
    $year = '';
    $first = ' first';

    foreach ($Events as $date => $events) {
        $prevDay = date("Y-m-d", prevWeekday(strtotime($date)));

        $yearstamp =
            "<section>\n\t\t\t" .
            '<div class="newYear%2$s">' .
            "\n\t\t\t\t" .
            '<a onClick="toggleYear(%1$d)" onmouseover="" class="hint--bottom-right" data-tippy-placement="bottom-start" data-tippy-content="Click to hide/show all %1$d timeline events" aria-label="Click to hide/show all %1$d timeline events"><time datetime="%1$d" id="y%1$d">%1$d</time></a>' .
            "\n\t\t\t</div><br>"; //hint--bottom-right

        if ($date === array_key_first($Events)) {
            // First event, so we must first display the year
            $year = substr($date, 0, 4);
            $output .= sprintf($yearstamp, $year, $first);
            $first = '';
        }

        // Check if all events within this date are hidden. If they are, do not bit flip $side
        $allHidden = true;
        $dateStamped = false;
//        while ($allHidden === false) {
//            if ($event['hidden']) $allHidden = true;
//        }
        $side = ($left) ? 'left' : 'right';

        // The year has changed, we insert the new year into the timeline
        if ($year != substr($date, 0, 4)) {
            $year = substr($date, 0, 4);
            $output .= sprintf("\n\t\t</section>\n\t\t" . $yearstamp, $year, $first);
        }

        foreach ($events as $i => $event) {
            // Skip hidden events
            if ($event->isHidden()) continue;
            $allHidden = false;

            $shareId = preg_replace("/[^A-Za-z0-9]/", '', $event->getHeadline()) . '-' . $date;
            $output .= sprintf(
                "\n\t\t\t" . '<article class="container %1$s %2$d" data-year="%2$d" data-month="%3$d" data-day="%4$d" data-tags="%5$s" data-key="%6$d" id="%7$s">',
                $side,
                $year,
                substr($date, 5, 2),
                substr($date, 8),
                TagsToCSV($event->getTags()),
                $i,
                $shareId
            );

            // Show the datestamp for only the first card of a date
//            $hidden = (array_key_first($events) === $i) ? '' : ' hidden';
            $hidden = (!$dateStamped) ? '' : ' hidden';
            $output .= sprintf("\n\t\t\t" . '<time datetime="%s" class="eventDate%s">%s</time>', $date, $hidden, date("M j", strtotime($date)));
            $icon = (!empty($event->getIcon())) ? $event->getIcon() : 'fas fa-power-off';
            $prevClose = (!empty($Prices[$prevDay])) ? $Prices[$prevDay]['close'] : null;
            $prices = (!empty($Prices[$date])) ? formatPrices($event->getOpen(), $event->getLow(), $event->getHigh(), $event->getClose(), $event->getVolume(), $prevClose) : '';

            $output .= sprintf(
                (
                    "\n\t\t\t\t" . '<i class="icon %s"></i>' .
                    "\n\t\t\t\t" . '<main class="content%s">' .
                    "\n\t\t\t\t\t<section>" .
                    "\n\t\t\t\t\t\t" . '<a onclick="shareTimelineEvent(\'%s\');" class="hint--right share" aria-label="Click to share this timeline event"><i class="share fas fa-share-alt"></i></a>' .
                    "\n\t\t\t\t\t\t<h2>%s</h2>" .
                    "\n\t\t\t\t\t\t<p>%s</p>" .
                    "\n\t\t\t\t\t</section>\n\t\t\t\t\t<footer>" .
                    "%s\n\t\t\t\t\t\t<q>%s</q>\n\t\t\t\t\t\t" . '<span class="tags">%s</span>' .
                    "\n\t\t\t\t\t</footer>\n\t\t\t\t</main>\n\t\t\t</article>"
                ),
                $icon,
                ($event->isHighlighted()) ? ' highlighted' : '',
                $shareId,
                $event->getHeadline(),
                str_replace("\n", "<br>", InjectImageLinks($event->getDescription())),
                formatSources($event->getSource()),
                $prices,
                TagsToCSV($event->getTags(), true),
            );

            // We have displayed the datestamp once for this date, bit flip so we don't display it again
            $dateStamped = true;
        }

        // Only bit flip the next timeline event's side of the vertical timeline divider if not all events of a date are hidden
        $left = ($allHidden) ? $left : !$left;
    }

    return $output;
}


function formatSources($sources, $cutoff = 65) {
    $output = "\n\t\t\t\t\t\t<ol>";

    if (is_string($sources)) {
        if (empty($sources)) return '';

        $sourceLength = strlen($sources);
        $sourceText = ($sourceLength > $cutoff) ? substr($sources, 0, $cutoff)  . '...' : $sources;

        $output .= sprintf(
            '%3$s<cite><a href="%1$s" title="%1$s" target="_blank">%2$s</a></cite>',
            $sources,
            $sourceText,
            "\n\t\t\t\t\t\t\t<li>"
        );
        $output .= "</li>\n\t\t\t\t\t\t</ol>";

        return $output;
    }

    foreach ($sources as $key => $source) {
        if (empty($source) || is_array($source)) continue; // is_array to handle bad input data, monkey errors :/

        $sourceLength = strlen($source);
        $sourceText = ($sourceLength > $cutoff) ? substr($source, 0, $cutoff)  . '...' : $source;

        $output .= sprintf(
            '%3$s<cite><a href="%1$s" title="%1$s" target="_blank">%2$s</a></cite></li>',
            $source,
            $sourceText,
            "\n\t\t\t\t\t\t\t<li>",
        );
    }

    $output .= "\n\t\t\t\t\t\t</ol>";

    return $output;
}


function formatPrices(float $open = 0.00, float $low = 0.00, float $high = 0.00, float $close = 0.00, int $volume = 0, $prevClose = null): string {
    $direction = '';

    // If GME closed higher than yesterday's previous close, display a green uptick
    if ($prevClose) {
        if ($close > $prevClose) {
            $direction =  '<i class="fas fa-sort-up green"></i>';
        } else if ($close < $prevClose) {
            $direction = '<i class="fas fa-sort-down red"></i>';
        } else {
            $direction =  '<i class="fas fa-sort"></i>';
        }
    }

    return sprintf(
        "<b>Open</b>: $%s, <b>Low</b>: $%s, <b>High</b>: $%s, <b>Close</b>: %s$%s, <b>Volume</b>: %s",
        formatPrice($open),
        formatPrice($low),
        formatPrice($high),
        $direction,
        formatPrice($close),
        number_format($volume, 0, '.', ',')
    );
}


// Get a list of all tags used in all events, prune duplicates, apply natural human sorting
function CompileTags(array $Events): array {
    $tags = [];

    foreach ($Events as $date => $events) {
        foreach ($events as $event) {
            $eventTags = $event->getTags();

            // Skip hidden events
            if ($event->isHidden()) continue;

            // Only one tag to add to the compilation
            if (is_string($eventTags)) {
                if (empty($eventTags)) continue;

                $tags[] = $eventTags;

                continue;
            }

            // Assume we're left with an array, now is the array empty, has only one value to add, or needs iterating?
            switch (count($eventTags)) {
                case 0:
                    break;
                case 1:
                    if (empty($eventTags[0])) break;

                    $tags[] = $eventTags[0];

                    break;
                default:
                    foreach($eventTags as $tag) {
                        if (empty($tag)) continue;

                        $tags[] = $tag;
                    }
                    break;
            }
        }
    }

    $tags = array_unique($tags);
    asort($tags, SORT_NATURAL | SORT_FLAG_CASE);

    return $tags;
}


// Compile tags for display in individual timeline event footers
// if $hashtag = false then we're compiling tags without the hashtag for use in a data-tags attribute
function TagsToCSV(string|array $tags, bool $hashtag = false): string {
    global $Events;

    // If there's only one tag, nothing else to do
    if (is_string($tags)) {
        if (!$tags) return '';

        return ($hashtag)
//            ? sprintf('<a onclick="filterAllTags(false); filterTag(\'%1$s\'); jumpYear(' . substr(array_key_first($Events), 0, 4) . ')">#%1$s</a>', $tags)
            ? sprintf('<a onclick="showOnly(\'%1$s\', this.parentNode.parentNode.parentNode.parentNode)">#%1$s</a>', $tags)
            : $tags;
    }

    if (is_array($tags) && $hashtag) {
        for ($i = 0; $i < count($tags); $i++) {
            if (!$tags[$i]) continue;

            $tags[$i] = sprintf('<a onclick="showOnly(\'%1$s\', this.parentNode.parentNode.parentNode.parentNode)">#%1$s</a>', $tags[$i]);
        }

        return implode(', ', $tags);
    }

    // There are multiple tags but no hashtag is needed, likely for a data-tags attribute
    return implode(',', $tags);
}


function FormatTags(array $tags): string {
    $output = '';

    foreach($tags as $key => $tag) {
        $output .= sprintf("\t\t" . '<div class="tagFilter active"><a onclick="filterTag(\'%1$s\')" class="active" id="tag-%1$s">#%1$s</a>' . "</div>\n", $tag);
    }

    return $output;
}


// https://coderwall.com/p/eqcpyg/get-the-previous-working-day
// This function will return the given time minus an appropriate amount of days so that the previous day falls on a weekday
function prevWeekday($time) {
    $currentWeekDay = date("w", $time);

    switch ($currentWeekDay) {
        case "1": { // Monday
            $days = 3;
            break;
        }
        case "0": { // Sunday
            $days = 2;
            break;
        }
        default: { // All other days
            $days = 1;
            break;
        }
    }

    return $time - (60 * 60 * 24 * $days);
}


function formatPrice($price) {
    $price = str_replace(['$', '€', '£'], '', $price); // Strip out currency symbols
    $thousands_separator = ','; // Use , for USD, comma is most popular

    $price	= preg_replace('/\s+/', '', $price); // Remove spaces
    $price	= floatval($price); // Convert to FLOAT

    // Return the formatted value, enforcing two decimal places every time
    return number_format($price, 2, '.', $thousands_separator);
}


function CountEvents(array $Events): string {
    $count = 0;

    foreach($Events as $date => $events) {
        foreach ($events as $event) {
            if (!$event->isHidden()) $count++;
        }
    }

    return $count;
}


function CountCitations(array $Events): string {
    $count = 0;

    foreach($Events as $date => $events) {
        foreach ($events as $event) {
            $source = $event->getSource();

            if (!$source) continue;
            if (is_array($source)) $count += count($source);
            if (is_string($source)) $count++;
        }
    }

    return $count;
}
