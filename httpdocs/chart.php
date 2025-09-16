<?php
// Load environment secrets
require_once('../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '../.env');
$dotenv->load();

// Give myself extra diagnostic info
// if ($_SERVER['REMOTE_ADDR'] === $_ENV['HOME_IP']) {
//     error_reporting(E_ALL); // Report all errors
//     ini_set('display_errors', 1); // See errors' stack trace
// //    phpinfo();
// }

$title = 'GameStop Short FTD Squeeze Timeline';
$url = 'gmetimeline.com';

require_once('../php/Event.class.php');
require_once('../php/ImportPrices.php');
require_once('../php/ImportEvents.php');
require_once('../php/Views.php');
require_once('../php/exportChartData.php');

global $Events, $EventsAsc, $LastModified, $Prices;

$exportData = ExportChartData($EventsAsc, 2020);
$labels = $exportData['labels'];
$prices = $exportData['prices'];
$xy = $exportData['xy'];
$data = '';
foreach ($xy as $datum) {
    $data .= (!empty($data)) ? ', ' : '';
    $data .= '{x: "' . $datum[0] . '", y: ' . $datum[1] . '}';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>🦍💎<?=$title;?>✋🚀</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?=$url;?> places events surrounding the GameStop short squeeze onto a timeline to make connecting the dots a whole lot easier">
    <meta name="keywords" content="GME,GameStop,timeline,short squeeze,FTD">

    <meta property="og:title" content="GME Short FTD Squeeze Timeline">
    <meta property="og:description" content="<?=$url;?> places events surrounding the GameStop short squeeze onto a timeline to make connecting the dots a whole lot easier">
    <meta property="og:type" content="website">
    <meta property="og:url" content="/">
    <meta property="og:image" content="/img/og-image2.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:alt" content="A vertical timeline of events relating to the GameStop short squeeze">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <link rel="stylesheet" type="text/css" href="/css/timeline.css">
    <link rel="stylesheet" type="text/css" href="/css/main.css">
    <link rel="stylesheet" type="text/css" href="/css/share.css">
    <link rel="stylesheet" type="text/css" href="/css/tips.css">
    <link rel="stylesheet" type="text/css" href="/css/hint.min.css">
    <link rel="stylesheet" type="text/css" href="/css/light.css" id="theme">
    <link rel="stylesheet" type="text/css" href="/css/stars.css">
    <link rel="stylesheet" type="text/css" href="/css/moon.css">
    <link rel="stylesheet" type="text/css" href="/css/rocket.css">

    <link rel="shortcut icon" href="/img/favicon.svg" type="image/svg">
</head>
<body>
<div id="top"></div>
<div id="snackbar"></div>

<div style="width: 3000px;">
    <canvas id="GameStopChart"></canvas>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.3.2/chart.min.js" integrity="sha512-VCHVc5miKoln972iJPvkQrUYYq7XpxXzvqNfiul1H4aZDwGBGC0lq373KNleaB2LpnC2a/iNfE5zoRYmB4TRDQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    // Chart.plugins.register(ChartRough);


    const skipped = (ctx, value) => ctx.p0.skip || ctx.p1.skip ? value : undefined;
    const down = (ctx, value) => ctx.p0.parsed.y > ctx.p1.parsed.y ? value : undefined;



    let myChart = new Chart(
        document.getElementById('GameStopChart'),
        {
            // plugins: [ChartRough],
            type: 'line',
            data: {
                //labels: <?//=json_encode($labels);?>//,
                datasets: [{
                    label: '',
                    backgroundColor: 'rgb(255, 99, 132)',
                    // borderColor: 'rgb(255, 99, 132)',
                    borderColor: 'rgb(75, 192, 192)',
                    data: [<?=$data;?>],
                    segment: {
                        borderColor: ctx => skipped(ctx, 'rgb(0,0,0,0.2)') || down(ctx, 'rgb(192,75,75)'),
                        borderDash: ctx => skipped(ctx, [6, 6]),
                    }
                }]
            },

            options: {
                scales: {
                    xAxes: {},
                    yAxes: {
                        ticks: {
                            // Include a dollar sign in the ticks
                            callback: function(value, index, values) {
                                // return '$' + value;
                                return value.toLocaleString("en-US",{style:"currency", currency:"USD"});
                            }
                        },
                        // type: 'logarithmic'
                    }
                },
                responsive: true,
                maintainAspectRatio: true,

                fill: false,
                interaction: {
                    intersect: false
                },
                radius: 0, // Removes dots on each datapoint

                plugins: {
                    legend: {
                        display: false, // Removes dataset color legend
                        position: 'top'
                    }
                }
            }
        }
    );
</script>

<?php //print_r($Events); ?>

<div id="bgStars"></div>
<div id="moon" class="moon">
    <ul>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
    <div class="diamond"><i class="icon far fa-gem"></i></div>
    <div class="hands"><i class="icon fas fa-hands"></i></div>
</div>
<div id="rocket" class="rocket">
    <div class="rocket-body">
        <div class="body"><span>GME</span></div>
        <div class="fin fin-left"></div>
        <div class="fin fin-right"></div>
        <div class="window"></div>
    </div>
    <div class="exhaust-flame"></div>
    <ul class="exhaust-fumes">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
    <ul class="star hidden">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
</div>

<!--<script src="/js/theme.js"></script>-->
<!--<script src="/js/timeline.js"></script>-->
<script src="/js/stars.js"></script>
<script src="/js/share.js"></script>
<script src="/js/tips.js"></script>
<script src="https://kit.fontawesome.com/a7f4b5ef3a.js" crossorigin="anonymous"></script>

<script async src="https://www.googletagmanager.com/gtag/js?id=G-KGW3YHQJ9V"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-KGW3YHQJ9V');
</script>

</body>
</html>
