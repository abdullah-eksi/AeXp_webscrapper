<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com');
$scrapper->setProxy('http://proxy-server:port');
$html = $scrapper->get();

echo $html; // HTML içeriğini ekrana yazdır
?>
