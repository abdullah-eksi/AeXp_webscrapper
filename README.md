
# AeXp_webscrapper

**AeXp_webscrapper**, PHP tabanlı bir web kazıma kütüphanesidir. Web sitelerinden veri çekmek, form göndermek ve DOM üzerinde sorgular gerçekleştirmek için kullanışlı bir yapı sunar. Bu kütüphane, geliştiricilerin web verilerini kolayca almasına ve işlemesine olanak tanır.

## İçindekiler

- [Kurulum](#kurulum)
- [Kullanım](#kullanım)
  - [Scrapper Sınıfı](#1-scrapper-sınıfı)
  - [Selector Sınıfı](#2-selector-sınıfı)
  - [FormHandler Sınıfı](#3-formhandler-sınıfı)
- [Metodlar](#metodlar)
- [Lisans](#lisans)

## Kurulum

Kütüphaneyi kullanmak için, aşağıdaki gibi ana dosyanızı oluşturun ve `lib.php` dosyasını dahil edin:

```php
<?php
require_once 'AeXp_webscrapper/lib.php';

// Kütüphaneyi kullanmaya başlayabilirsiniz
?>
```

## Kullanım

### 1. Scrapper Sınıfı

**Scrapper** sınıfı, HTTP istekleri yapmak ve web sayfalarından veri çekmek için kullanılır.

#### Metodlar

- `__construct($url, $timeout = 30)`: Sınıfın örneğini oluşturur.
  - **Parametreler**:
    - `$url`: Hedef URL.
    - `$timeout`: (Opsiyonel) İstek zaman aşımı süresi (saniye).
  
- `get()`: Belirtilen URL'den HTML içeriğini çeker.
  - **Dönüş Değeri**: HTML içeriği (string).
  
- `post($data)`: Belirtilen URL'ye POST isteği yapar.
  - **Parametreler**:
    - `$data`: Gönderilecek form verilerini içeren dizi.
  - **Dönüş Değeri**: Sunucudan dönen yanıt (string).
  
- `setProxy($proxy)`: Proxy ayarını belirler.
  - **Parametreler**:
    - `$proxy`: Proxy sunucu adresi.
  
- `setRandomUserAgent()`: Rastgele bir kullanıcı ajanı ayarlamak için kullanılır.

#### Örnek Kullanımlar

##### Örnek 1: Basit GET İsteği

```php
<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com');
$html = $scrapper->get();

echo $html; // HTML içeriğini ekrana yazdır
?>
```

##### Örnek 2: POST İsteği Gönderimi

```php
<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com/login');
$response = $scrapper->post([
    'username' => 'testuser',
    'password' => 'securepassword'
]);

echo $response; // POST isteği sonucunu ekrana yazdır
?>
```

##### Örnek 3: Proxy Kullanımı

```php
<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com');
$scrapper->setProxy('http://proxy-server:port');

$html = $scrapper->get();
echo $html; // HTML içeriğini ekrana yazdır
?>
```

### 2. Selector Sınıfı

**Selector** sınıfı, çekilen HTML içeriği üzerinde eleman seçimi yapmak için kullanılır.

#### Metodlar

- `__construct($dom)`: DOM nesnesini alır ve saklar.
  
- `selectByXPath($query)`: XPath sorgusu ile elemanları seçer.
  - **Parametreler**:
    - `$query`: XPath sorgusu.
  - **Dönüş Değeri**: Seçilen elemanlar (dizi).

- `selectByCssSelector($tag = '*', $class = null, $id = null)`: CSS seçici ile elemanları seçer.
  - **Parametreler**:
    - `$tag`: (Opsiyonel) Seçilecek HTML etiket türü.
    - `$class`: (Opsiyonel) Sınıf adı.
    - `$id`: (Opsiyonel) ID adı.
  - **Dönüş Değeri**: Seçilen elemanlar (dizi).

#### Örnek Kullanımlar

##### Örnek 4: DOM'dan Eleman Seçimi

```php
<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com');
$dom = $scrapper->getDom(); // HTML içeriğini DOM nesnesine çevir

$selector = new Selector($dom);
$links = $selector->selectByCssSelector('a'); // Tüm <a> etiketlerini seç

foreach ($links as $link) {
    echo "Link: " . $link['href'] . " - Metin: " . $link['text'] . "<br>";
}
?>
```

##### Örnek 5: XPath Kullanarak Seçim

```php
<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com');
$dom = $scrapper->getDom(); // HTML içeriğini DOM nesnesine çevir

$selector = new Selector($dom);
$articles = $selector->selectByXPath('//article'); // <article> etiketlerini seç

foreach ($articles as $article) {
    echo "Başlık: " . $article['text'] . "<br>";
}
?>
```

### 3. FormHandler Sınıfı

**FormHandler** sınıfı, form verilerini yönetmek ve göndermek için kullanılır.

#### Metodlar

- `__construct($scrapper)`: FormHandler nesnesini oluşturur.
  - **Parametreler**:
    - `$scrapper`: Scrapper nesnesi.
  
- `addField($name, $value)`: Form alanı ekler.
  - **Parametreler**:
    - `$name`: Alan adı.
    - `$value`: Alan değeri.
  
- `submit()`: Form verilerini gönderir.
  - **Dönüş Değeri**: Gönderim sonucu (dizi).

- `getErrors()`: Hataları döndürür (eğer varsa).
  - **Dönüş Değeri**: Hata mesajları (dizi).

#### Örnek Kullanımlar

##### Örnek 6: Form Gönderimi

```php
<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com/form');
$formHandler = new FormHandler($scrapper);

$formHandler->addField('email', 'test@example.com');
$formHandler->addField('password', 'securePassword123');

$response = $formHandler->submit(); // Form verilerini gönder

if ($response['success']) {
    echo 'Form başarıyla gönderildi!';
} else {
    echo 'Hatalar: ';
    print_r($response['errors']); // Hata mesajlarını yazdır
}
?>
```

##### Örnek 7: Form Verisi Doğrulama

```php
<?php
require_once 'AeXp_webscrapper/lib.php';

$scrapper = new Scrapper('https://example.com/form');
$formHandler = new FormHandler($scrapper);

$formHandler->addField('email', 'invalid-email'); // Geçersiz e-posta
$formHandler->addField('password', 'short'); // Kısa şifre

if (!$formHandler->submit()) {
    echo 'Hatalar: ';
    print_r($formHandler->getErrors()); // Hata mesajlarını yazdır
}
?>
```

## Metodlar

### Scrapper Sınıfı Metodları

- `__construct($url, $timeout = 30)`: Kütüphaneyi başlatır.
- `get()`: HTML içeriğini çeker.
- `post($data)`: POST isteği yapar.
- `setProxy($proxy)`: Proxy ayarlarını yapar.
- `setRandomUserAgent()`: Rastgele bir kullanıcı ajanı ayarlar.

### Selector Sınıfı Metodları

- `__construct($dom)`: DOM nesnesini alır.
- `selectByXPath($query)`: XPath sorgusu ile seçim yapar.
- `selectByCssSelector($tag = '*', $class = null, $id = null)`: CSS seçici ile seçim yapar.

### FormHandler Sınıfı Metodları

- `__construct($scrapper)`: FormHandler'ı başlatır.
- `addField($name, $value)`: Form alanı ekler.
- `submit()`: Formu gönderir.
- `getErrors()`: Hata mesajlarını döner.


## Lisans

Bu kütüphane MIT lisansı ile lisanslanmıştır. Daha fazla bilgi için LICENSE dosyasına bakabilirsiniz.

## Developed By Abdullah Ekşi
