<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com/blog');
$dom = $scrapper->getDom();

$selector = new Selector($dom);
$articles = $selector->selectByCssSelector('.article');

foreach ($articles as $article) {
    echo "Makale Başlığı: " . $article['text'] . "<br>";
    echo "Link: " . $article['href'] . "<br><br>";
}
?>
