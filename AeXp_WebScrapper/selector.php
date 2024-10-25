<?php

class Selector
{
    // DOM belgesini tutan özel değişken
    private $dom;

    // Yapıcı metod; DOM belgesi alır ve saklar
    public function __construct($dom)
    {
        $this->dom = $dom;
    }

    // XPath ile elementleri seçen metod
    public function selectByXPath($query)
    {
        // Yeni bir DOMXPath nesnesi oluştur
        $xpath = new DOMXPath($this->dom);
        // Verilen XPath sorgusunu uygula ve düğümleri al
        $nodes = @$xpath->query($query);

        // Eğer sorgu geçersizse hata fırlat
        if ($nodes === false) {
            throw new Exception('Geçersiz XPath ifadesi: ' . $query);
        }

        // Düğümleri diziye dönüştür ve geri döndür
        return $this->nodesToArray($nodes);
    }

    // CSS seçici ile elementleri seçen metod
    public function selectByCssSelector($tag = '*', $class = null, $id = null)
    {
        // Virgülle ayrılmış tag'leri diziye ayır
        $tags = explode(',', $tag);
        $xpathQueries = [];
    
        // Her bir tag için XPath sorgusunu oluştur
        foreach ($tags as $singleTag) {
            $xpathQuery = $this->cssToXPath(trim($singleTag), $class, $id);
            $xpathQueries[] = $xpathQuery; // Oluşturulan sorguyu diziye ekle
        }
    
        // Tüm XPath sorgularını birleştir
        $combinedQuery = implode('|', $xpathQueries);
        
        // Birleştirilmiş sorguyla düğümleri seç
        return $this->selectByXPath($combinedQuery);
    }

    // Node'ları diziye dönüştüren özel metod
    private function nodesToArray($nodes)
    {
        $result = []; // Sonuç dizisini başlat
        foreach ($nodes as $node) {
            // Eğer düğüm DOMElement türündeyse
            if ($node instanceof DOMElement) {
                // Düğüm bilgilerini diziye ekle
                $result[] = [
                    'tag' => $node->tagName, // Tag adı
                    'text' => trim($node->textContent), // Düğüm metni
                    'src' => $node->getAttribute('src'), // src niteliği
                    'href' => $node->getAttribute('href'), // href niteliği
                    'title' => $node->getAttribute('title'), // title niteliği
                    'class' => $node->getAttribute('class'), // class niteliği
                ];
            }
        }
        return $result; // Sonuç dizisini geri döndür
    }

    // CSS seçiciyi XPath'e dönüştüren özel metod
    private function cssToXPath($tag, $class, $id)
    {
        // Başlangıçta tag'i al
        $xpath = "//" . ($tag ?: '*');

        // Eğer ID varsa ekleyelim
        if ($id) {
            $xpath .= "[@id='$id']"; // ID seçimi için koşul ekle
        }

        // Eğer sınıf varsa
        if ($class) {
            // Sınıf isimlerini ayırıyoruz
            $classes = explode(' ', trim($class));
            foreach ($classes as $c) {
                // Sınıf adını içeren bir XPath koşulu ekle
                $xpath .= "[contains(concat(' ', normalize-space(@class), ' '), ' $c ')]";
            }
        }

        return $xpath; // Oluşturulan XPath sorgusunu geri döndür
    }
}
