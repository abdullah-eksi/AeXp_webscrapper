<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com');
$dom = $scrapper->getDom();

$selector = new Selector($dom);
$headings = $selector->selectByCssSelector('h1, h2, h3');

foreach ($headings as $heading) {
    echo "Başlık: " . $heading['text'] . "<br>";
}
?>
