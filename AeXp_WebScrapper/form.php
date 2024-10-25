<?php

class FormHandler
{
    // Scrapper nesnesi, form verileri ve hata mesajlarını tutan özel değişkenler
    private $scrapper;
    private $formData;
    private $errors;

    // Yapıcı metod; Scrapper nesnesini alır
    public function __construct($scrapper)
    {
        $this->scrapper = $scrapper; // Scrapper nesnesini sakla
        $this->formData = []; // Form verilerini başlat
        $this->errors = []; // Hata mesajlarını başlat
    }

    // Form verisi ekleme metod
    public function addField($name, $value)
    {
        // Veriyi temizle ve sakla
        $this->formData[$name] = htmlspecialchars(trim($value)); // Güvenlik için veri temizleme
    }

    // Form verilerini doğrulama metod
    public function validate()
    {
        // Burada form verilerini doğrulamak için özel kurallar ekleyebilirsiniz
        foreach ($this->formData as $name => $value) {
            // Boş alan kontrolü
            if (empty($value)) {
                $this->errors[$name] = "{$name} alanı boş bırakılamaz."; // Hata mesajı ekle
                continue; // Hata bulduğumuzda devam et
            }
            // Geçerli e-posta kontrolü
            if ($name === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->errors[$name] = "Geçersiz e-posta adresi."; // Hata mesajı ekle
            }
            // Diğer doğrulama kuralları ekleyin
            // Örneğin, şifre için en az 8 karakter ve büyük harf kontrolü
            if ($name === 'password' && strlen($value) < 8) {
                $this->errors[$name] = "Şifre en az 8 karakter olmalıdır."; // Hata mesajı ekle
            }
        }
        // Hatalar varsa false, yoksa true döndür
        return empty($this->errors);
    }

    // Hataları döndüren metod
    public function getErrors()
    {
        return $this->errors; // Hata mesajlarını geri döndür
    }

    // Form verisini POST isteği olarak gönderme metod
    public function submit()
    {
        // Öncelikle verileri doğrula
        if (!$this->validate()) {
            return [
                'success' => false,
                'errors' => $this->getErrors() // Hata varsa döndür
            ];
        }

        // Proxy ve user-agent rotasyonu
        $this->scrapper->setRandomUserAgent(); // Rastgele kullanıcı ajanı ayarla
        $this->scrapper->setRandomReferer(); // Rastgele referans ayarla
        
        try {
           
            $response = $this->scrapper->post($this->formData);
            return [
                'success' => true,
                'response' => $response 
            ];
        } catch (Exception $e) {
          
            return [
                'success' => false,
                'errors' => ['global' => 'Form gönderimi sırasında bir hata oluştu: ' . $e->getMessage()] 
            ];
        }
    }
}
