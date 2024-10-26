<?php

class FormHandler
{
    // Scrapper nesnesi, form verileri ve hata mesajlarını tutan özel değişkenler
    private $scrapper;
    private $formData;
    private $errors;
    private $requiredFields;

    // Yapıcı metod; Scrapper nesnesini alır ve başlangıç ayarlarını yapar
    public function __construct($scrapper)
    {
        $this->scrapper = $scrapper; // Scrapper nesnesini sakla
        $this->formData = []; // Form verilerini başlat
        $this->errors = []; // Hata mesajlarını başlat
        $this->requiredFields = []; // Gerekli alanları başlat
    }

    // Form verisi ekleme metod
    public function addField($name, $value, $isRequired = false)
    {
        // Veriyi temizle ve sakla
        $this->formData[$name] = htmlspecialchars(trim($value)); // Güvenlik için veri temizleme
        if ($isRequired) {
            $this->requiredFields[] = $name;
        }
    }

    // Form verilerini doğrulama metod
    public function validate()
    {
        foreach ($this->formData as $name => $value) {
            // Boş alan kontrolü
            if (in_array($name, $this->requiredFields) && empty($value)) {
                $this->errors[$name] = "{$name} alanı boş bırakılamaz."; // Hata mesajı ekle
                continue;
            }

            // E-posta kontrolü (e-posta alanı varsa)
            if ($name === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->errors[$name] = "Geçersiz e-posta adresi."; // Hata mesajı ekle
            }

            // Şifre için kontrol (örnek)
            if ($name === 'password' && strlen($value) < 8) {
                $this->errors[$name] = "Şifre en az 8 karakter olmalıdır."; // Hata mesajı ekle
            }
        }
        
        return empty($this->errors); // Hatalar varsa false, yoksa true döndür
    }

    // Hataları döndüren metod
    public function getErrors()
    {
        return $this->errors; // Hata mesajlarını geri döndür
    }

    // Başarı durumunda özel mesaj ekleyerek döndüren metod
    public function submit()
    {
        if (!$this->validate()) {
            return [
                'success' => false,
                'errors' => $this->getErrors()
            ];
        }

        // Proxy ve user-agent rotasyonu
        $this->scrapper->setRandomUserAgent();
        $this->scrapper->setRandomReferer();
        
        try {
            $response = $this->scrapper->post($this->formData);

            return [
                'success' => true,
                'message' => 'Form başarıyla gönderildi.',
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
