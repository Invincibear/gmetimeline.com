<?php
// TODO: https://whalewisdom.com/filer/maplelane-capital-llc#tabholdings_tab_link


// Load environment secrets
require_once('../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '../.env');
$dotenv->load();

// Give myself extra diagnostic info
if ($_SERVER['REMOTE_ADDR'] === $_ENV['HOME_IP']) {
    error_reporting(E_ALL); // Report all errors
    ini_set('display_errors', 1); // See errors' stack trace
//    phpinfo();
}

$title = 'GameStop Short FTD Squeeze Timeline';
$url = $_ENV['URL'];

require_once('../php/Event.class.php');
require_once('../php/ImportPrices.php');
require_once('../php/ImportEvents.php');
require_once('../php/Views.php');

global $Events, $LastModified, $Prices;

$totalEvents    = CountEvents($Events);
$totalDates     = count($Events);
$totalCitations = CountCitations($Events);
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
    <link rel="stylesheet" type="text/css" href="/css/dark.css" id="theme">

    <link rel="preload" as="style" type="text/css" href="/css/hint.min.css" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" as="style" type="text/css" href="/css/stars.css" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" as="style" type="text/css" href="/css/moon.css" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" as="style" type="text/css" href="/css/rocket.css" onload="this.onload=null;this.rel='stylesheet'">

    <link rel="shortcut icon" href="/img/favicon.svg" type="image/svg">
</head>
<body>
<div id="top"></div>
<div id="snackbar"></div>

<header class="header">
    <div id="jumpToYear">
        <label for="jumpYear" class="hidden">Jump To Year:</label>
        <select id="jumpYear" onchange="jumpYear(this.value)">
            <optgroup label="Jump To Year">
            <?=FormatYears($Events);?>

                <option value="moon">The Start</option>
            </optgroup>
        </select>
    </div>
    <div id="changeTheme">
        <!--        <b>Theme</b>:-->
        <a onclick="changeTheme('light');"><i class="fas fa-sun light" aria-hidden="true"></i> light</a>
        &nbsp;|&nbsp;
        <a onclick="changeTheme('dark');">dark <i class="fas fa-moon dark" aria-hidden="true"></i></a>
        |
        <a onclick="toggleAnimations();" id="animationLink">stop animations</a>
    </div>
    <h1><?=$title;?></h1>
    <div id="tagFilters">
        <!--            <b>Filter by tags</b><br style="clear:both;">-->
        <div class="tagFilter active"><a onclick="filterAllTags(true)" class="active" id="tag-ALL">Show All</a></div>
        <div class="tagFilter inactive"><a onclick="filterAllTags(false)" class="inactive" id="tag-NONE">Hide All</a></div>
        <div class="tagFilter inactive"><a onclick="hideHeadlines()" class="inactive" id="tag-headlines">Headlines Only</a></div>
        <br style="clear:both;">
<?=FormatTags(CompileTags($Events));?>
        <br style="clear:both;">
    </div>
</header>
<main class="content">
    <div class="timeline">
        <?=FormatEvents($Events);?>

        </section></div>
</main>
<footer class="footer">
    <div id="liftoff"><a onclick="scrollToTop()" title="Return to top of page"><i class="fas fa-rocket"></i></a></div>
    <p><strong>Disclaimer</strong>: This is not financial advice and <?=$url;?> makes no guarantees of the accuracy of the presented information. Verify everything on your own before you make decisions based on this information. <?=$url;?> is not affiliated with GameStop or any other person or entity named within</p>
    <p>Currently displaying <?=$totalEvents;?> events from <?=$totalDates;?> dates with <?=$totalCitations;?> citations on the timeline.<!-- Events last updated <?=date("M j, Y", $LastModified[0]);?>--></p>
    <p>Did we miss something or make a mistake? <a onclick="openTips()" class="tips">Click here to anonymously send us a tip</a></p>
    <p>Please credit us if this timeline helps you with any journalistic endeavors</p>
</footer>

<div id="tips" class="hidden">
    <form>
        <label for="tip" class="hidden">Submit your tip:</label>
        <textarea id="tip" name="tip" placeholder="Please let us know about any corrections or additional information"></textarea>
        <button type="button" onclick="submitTips()">Send Tip</button>
        <button type="button" onclick="closeTips()">Close Form</button>
    </form>
</div>

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
<div id="bottom"><br></div>

<script src="/js/theme.js"></script>
<script src="/js/timeline.js"></script>
<script src="/js/stars.js" defer></script>
<script src="/js/share.js"></script>
<script src="/js/tips.js" defer></script>
<script src="https://kit.fontawesome.com/2694890063.js" crossorigin="anonymous"></script>

<script async src="https://www.googletagmanager.com/gtag/js?id=G-KGW3YHQJ9V"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-KGW3YHQJ9V');
</script>

</body>
</html>
