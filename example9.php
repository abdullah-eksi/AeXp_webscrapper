<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com/gallery');
$dom = $scrapper->getDom();

$selector = new Selector($dom);
$images = $selector->selectByCssSelector('img');

foreach ($images as $image) {
    echo "Resim URL: " . $image['src'] . "<br>";
    echo "Alternatif Metin: " . $image['alt'] . "<br><br>";
}
?>
