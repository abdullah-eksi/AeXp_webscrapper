<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com');
$dom = $scrapper->getDom();

$selector = new Selector($dom);
$links = $selector->selectByCssSelector('a');

foreach ($links as $link) {
    echo "Link: " . $link['href'] . " - Metin: " . $link['text'] . "<br>";
}
?>
