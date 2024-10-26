<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://practice.expandtesting.com/authenticate');

// Form verilerini ekle
$formHandler = new FormHandler($scrapper);
$formHandler->addField('username', 'practice' ,true); // Kullanıcı adı
$formHandler->addField('password', 'SuperSecretPassword!',true);  // Şifre

// Gönderim sonucu
$response = $formHandler->submit();

if ($response['success']) {
    echo "Yanıt: ";
    echo $response['response']; // Başarılı yanıtı ekrana yazdır
} else {
    echo "Hatalar: ";
    print_r($response['errors']); // Hata varsa ekrana yazdır
}
?>
