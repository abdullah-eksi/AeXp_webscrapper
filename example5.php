<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com');
$dom = $scrapper->getDom();

$selector = new Selector($dom);
$articles = $selector->selectByXPath('//article');

foreach ($articles as $article) {
    echo "Başlık: " . $article['text'] . "<br>";
}
?>
