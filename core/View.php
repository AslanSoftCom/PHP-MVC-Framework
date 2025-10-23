<?php

class View {

    /**
     * Bu metod, belirtilen bir görünüm dosyasını yükler ve isteğe bağlı olarak
     * verilmiş verileri (data) görünüm dosyasına aktarır.
     *
     * @param string $file Görünüm dosyasının bulunduğu ana klasör.
     * @param string $path Görünüm dosyasının yolu (noktalar `/` olarak değiştirilir).
     * @param array $data Görünüm dosyasına aktarılacak veriler. (Varsayılan: boş dizi)
     * @throws RuntimeException Eğer görünüm dosyası bulunamazsa hata fırlatır.
     */
    public function render(string $file, string $path, array $data = []): void {
        // Path traversal saldırılarına karşı koruma
        $safePath = str_replace(['../', '..\\'], '', $path);
        $viewFile = __DIR__ . '/../app/' . $file . '/views/' . str_replace('.', '/', $safePath) . '.php';
        
        if (!file_exists($viewFile)) {
            throw new RuntimeException("View file not found: {$viewFile}");
        }

        // EXTR_SKIP ile mevcut değişkenlerin üzerine yazılmasını engelle
        extract($data, EXTR_SKIP);
        require $viewFile;
    }
}
