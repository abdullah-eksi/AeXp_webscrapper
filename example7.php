<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com/api/data');
$html = $scrapper->get();
$data = json_decode($html, true);

echo "<pre>";
print_r($data); // JSON verisini ekrana yazdır
echo "</pre>";
?>
