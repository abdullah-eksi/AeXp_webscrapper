<?php

class Scrapper
{
    // URL, zaman aşımı, başlıklar, çerezler, proxy, kullanıcı ajanları ve referansları saklamak için özel değişkenler
    private $url;
    private $timeout;
    private $headers;
    private $cookies;
    private $proxy;
    private $userAgents;
    private $referers;
    private $cookieFile; // Oturum yönetimi için çerezleri saklamak üzere

    // Yapıcı metod; URL ve zaman aşımı ayarlarını alır
    public function __construct($url, $timeout = 30)
    {
        $this->url = $url; // URL'yi sakla
        $this->timeout = $timeout; // Zaman aşımını ayarla
        $this->headers = []; // Başlıkları başlat
        $this->cookies = []; // Çerezleri başlat
        $this->proxy = null; // Proxy ayarını başlat
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'cookies'); // Geçici çerez dosyası oluştur

        // Rastgele seçilen kullanıcı ajanları
        $this->userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Mobile Safari/537.36'
        ];

        // Rastgele seçilen referanslar
        $this->referers = [
            'https://google.com',
            'https://bing.com',
            'https://yahoo.com',
            'https://duckduckgo.com'
        ];
    }

    // GET isteği yapar
    public function get()
    {
        return $this->makeRequest('GET'); // GET isteğini gerçekleştirin
    }

    // POST isteği yapar
    public function post($data)
    {
        return $this->makeRequest('POST', $data); // POST isteğini gerçekleştirin
    }

    // Proxy ayarlarını belirler
    public function setProxy($proxy)
    {
        $this->proxy = $proxy; // Proxy ayarını sakla
    }

    // Rastgele gecikme ekler
    private function randomDelay($min = 2, $max = 5)
    {
        $delay = rand($min, $max); // Rastgele bir gecikme süresi belirle
        sleep($delay); // Gecikmeyi uygula
    }

    // cURL isteği yapar
    private function makeRequest($method, $data = null)
    {
        $ch = curl_init(); // Yeni bir cURL oturumu başlat
        curl_setopt($ch, CURLOPT_URL, $this->url); // URL'yi ayarla
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Yanıtı döndür
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout); // Zaman aşımını ayarla
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Yönlendirmeleri takip et
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10); // Maksimum yönlendirme sayısını ayarla

        // Rastgele bir kullanıcı ajanı ve referans ayarla
        $userAgent = $this->userAgents[array_rand($this->userAgents)];
        $referer = $this->referers[array_rand($this->referers)];
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent); // Kullanıcı ajanını ayarla
        curl_setopt($ch, CURLOPT_REFERER, $referer); // Referansı ayarla

        // Başlıkları ayarla
        if (!empty($this->headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers); // Başlıkları ekle
        }

        // Çerez yönetimi
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile); // Çerez dosyasından yükle
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile); // Çerez dosyasına kaydet

        // Proxy ayarları
        if (!empty($this->proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy); // Proxy ayarını uygula
        }

        // POST isteği için
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true); // POST isteği olduğunu belirt
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // POST verilerini ayarla
        }

        $this->randomDelay(); // İstekler arasında rastgele gecikme ekle

        $result = curl_exec($ch); // cURL isteğini gerçekleştir

        // cURL hatalarını kontrol et
        if (curl_errno($ch)) {
            throw new Exception('CURL Hatası: ' . curl_error($ch)); // Hata varsa istisna fırlat
        }

        curl_close($ch); // cURL oturumunu kapat
        return $result; // Sonucu geri döndür
    }

    // Rastgele bir kullanıcı ajanı ayarlamak için metod
    public function setRandomUserAgent()
    {
        $this->headers[] = 'User-Agent: ' . $this->userAgents[array_rand($this->userAgents)]; // Rastgele bir kullanıcı ajanı ekle
    }

    // Rastgele bir referans ayarlamak için metod
    public function setRandomReferer()
    {
        $this->headers[] = 'Referer: ' . $this->referers[array_rand($this->referers)]; // Rastgele bir referans ekle
    }

    // Çerez eklemek için metod
    public function addCookie($name, $value)
    {
        $this->cookies[] = "$name=$value"; // Çerezleri diziye ekle
    }

    // HTML içeriğinden DOM nesnesi almak için metod
    public function getDom()
    {
        $htmlContent = $this->get(); // HTML içeriğini al
        if (!$htmlContent) {
            throw new Exception('HTML içeriği alınamadı.'); // İçerik alınamazsa hata fırlat
        }
        $dom = new DOMDocument(); // Yeni bir DOMDocument nesnesi oluştur
        @$dom->loadHTML($htmlContent); // HTML içeriğini yükle
        return $dom; // DOM nesnesini geri döndür
    }

    // Temizlik: İşlem tamamlandığında çerez dosyasını sil
    public function __destruct()
    {
        if (file_exists($this->cookieFile)) {
            unlink($this->cookieFile); // Çerez dosyasını sil
        }
    }
}
