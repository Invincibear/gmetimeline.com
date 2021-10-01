<?php

function ExportChartData(array $Events, int $startYear = 2020): array {
    $labels = [];
    $prices = [];
    $xy = [];

    foreach ($Events as $date => $events) {
        // Check if all events within this date are hidden. If they are, do not bit flip $side
        $allHidden = true;

        // Enforce minimum start year

        if (substr($date, 0, 4) < $startYear) continue;

        foreach ($events as $i => $event) {
            // Skip hidden events, only iterate through one event per date
            if ($event->isHidden() || !$allHidden) continue;
            $allHidden = false;

            $price = formatPrice($events[$i]->getClose());
            $price = ($price === '0.00') ? 'NaN' : $price;
            $prices[] = $price;
            $labels[] = $date;
            $xy[] = [$date, $price];
        }
    }

    return [
        'labels' => $labels,
        'prices' => $prices,
        'xy' => $xy
    ];
}