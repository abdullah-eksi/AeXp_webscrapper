<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com/form');
$formHandler = new FormHandler($scrapper);

$formHandler->addField('email', 'test@example.com');
$formHandler->addField('password', 'securePassword123');

$response = $formHandler->submit();

if ($response['success']) {
    echo 'Form başarıyla gönderildi!';
} else {
    echo 'Hatalar: ';
    print_r($response['errors']);
}
?>
