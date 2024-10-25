<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://havadurumu15gunluk.xyz/havadurumu3aylik/630/istanbul-hava-durumu-90-gunluk.html');
$dom = $scrapper->getDom();

$selector = new Selector($dom);
$tds = $selector->selectByCssSelector('td');

$veriler = [];
$gunlukVeri = [];
$index = 0;

foreach ($tds as $td) {
    $text =$td['text'];


    switch ($index % 5) {
        case 0:
            $gunlukVeri['tarih'] = $text;
            break;
        case 1:
            $gunlukVeri['durum'] = $text;
            break;
        case 2:
            $gunlukVeri['yagis'] = $text;
            break;
        case 3:
            $gunlukVeri['en_yuksek'] = $text;
            break;
        case 4:
            $gunlukVeri['en_dusuk'] = $text;
            $veriler[] = $gunlukVeri;
            $gunlukVeri = []; 
            break;
    }
    $index++;
}


echo '<pre>';
print_r($veriler);
echo '</pre>';
