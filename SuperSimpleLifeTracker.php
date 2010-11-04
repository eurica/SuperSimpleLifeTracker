<?php
$feeds = array(
    'http://feeds.feedburner.com/matthewyglesias',
    'http://feeds.bbci.co.uk/news/rss.xml',
    );
    
$cacheLocation = '../layout/templates_c'; //needs to be somewhere writeable
$cacheTimeInHours = 2;
$pathToSimplePie = '../layout/SimplePie/simplepie.inc';
$itemsPerRSSFeed = 3; 

if(@$_REQUEST['feed']) {
$whichfeed = $_REQUEST['feed'];
} else $whichfeed = 0;
//SimplePie 1.2 throws DEPRECATED about 100 times on PHP 5.2
$current_error_reporting_level = error_reporting(); 
error_reporting($current_error_reporting_level ^ E_DEPRECATED); 

function shorten($summary, $length=200) {
	$summary = strip_tags($summary); //For XSS, I'm going to run everything we print out through here.
	if(strlen($summary) > $length) {
		$summary = substr($summary, 0, $length-3)."...";
	}
	return $summary;
}

    require_once($pathToSimplePie);
    $feed = new SimplePie();
	$feed->set_feed_url($feeds[$whichfeed]);
    $feed->enable_cache(true);
    $feed->set_cache_location($cacheLocation );
    $feed->set_cache_duration($cacheTimeInHours * 3600);
    $feed->set_item_limit($itemsPerRSSFeed);
    $feed->init();
    $feed->handle_content_type();
?>

<div class="twit">
	<ul id="twitList1" class="twitBody">
<?php foreach ($feed->get_items(0,$itemsPerRSSFeed) as $item) {
	$thisfeed = $item->get_feed(); 
	$text = $item->get_description();
	$text = shorten($text,140);
	$text = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+", "<a href=\"\\0\">\\0</a>", $text); //URLs
	
	if(strlen($item->get_description())>141) {
		$text.="<a href=". shorten($item->get_permalink(),512) ." title='From ". shorten($thisfeed->get_title()) ."'>more</a>";
	}
	
	?>
	<li class="twitEntry"> <span><?= $text; ?></span></li>
<?php } ?>
	</ul></div>

<?
	error_reporting($current_error_reporting_level); 
?>
