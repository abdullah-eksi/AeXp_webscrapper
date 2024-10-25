<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com/login');
$response = $scrapper->post([
    'username' => 'testuser',
    'password' => 'securepassword'
]);

echo $response; // Yanıtı ekrana yazdır
?>
