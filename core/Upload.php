<?php

class Upload  {

    /**
     * İzin verilen dosya formatları
     */
    private array $allowedTypes = ['png', 'jpg', 'jpeg'];
    
    /**
     * Belirtilen dizine dosya yükleme işlemi yapar
     * 
     * @param string $sourcePath  Yüklenecek dosyanın kaynak yolu
     * @param string $uploadPath  Dosyanın kaydedileceği hedef dizin
     * 
     * @return array Aşağıdaki anahtarları içeren durum dizisi:
     *               - status: bool - başarılı ise true, başarısız ise false
     *               - message: string - başarı/hata mesajı
     *               - path: string - yüklenen dosyanın hedef yolu (sadece başarılı ise)
     */
    public function images(string $sourcePath, string $uploadPath): array 
    {
        if (!file_exists($sourcePath)) {
            return ['status' => false, 'message' => 'Source file not found'];
        }

        // Dosya tipini kontrol et (MIME type ile daha güvenli)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $sourcePath);
        finfo_close($finfo);
        
        $allowedMimes = ['image/png', 'image/jpeg', 'image/jpg'];
        if (!in_array($mimeType, $allowedMimes)) {
            return ['status' => false, 'message' => 'Invalid file format'];
        }

        $fileType = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        if (!in_array($fileType, $this->allowedTypes)) {
            return ['status' => false, 'message' => 'Invalid file extension'];
        }

        // Güvenli dizin oluşturma (0755 yerine 0777)
        $uploadPath = rtrim($uploadPath, '/');
        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0755, true)) {
            return ['status' => false, 'message' => 'Failed to create upload directory'];
        }
        
        // Güvenli rastgele dosya adı oluştur
        try {
            $randomId = bin2hex(random_bytes(16));
        } catch (Exception $e) {
            $randomId = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 32);
        }
        
        $destination = $uploadPath . '/' . $randomId . '.' . $fileType;
        
        // Dosyayı kopyala ve izinlerini ayarla
        if (copy($sourcePath, $destination)) {
            chmod($destination, 0644);
            return ['status' => true, 'message' => 'File uploaded successfully', 'path' => $destination];
        }
        
        return ['status' => false, 'message' => 'Failed to save file'];
    }
}
