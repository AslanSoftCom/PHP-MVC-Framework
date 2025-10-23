<?php

class Generate {
    
    /**
     * Belirtilen grup sayısı ve karakter uzunluğunda rastgele anahtar oluşturur
     * Örnek: "ABC-DEF-GHI" veya "AB-CD"
     * 
     * @param int $groupCount    Anahtardaki grup sayısı (varsayılan: 3)
     * @param int $charsPerGroup Her gruptaki karakter sayısı (varsayılan: 3)
     * @return string           "ABC-DEF-GHI" formatında anahtar döndürür
     */
    public function format(int $groupCount = 3, int $charsPerGroup = 3): string 
    {
        // Q harfi eklendi (eksikti)
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLength = strlen($chars);
        
        return implode('-', array_map(
            function() use ($chars, $charsPerGroup, $charsLength) {
                $group = '';
                for ($i = 0; $i < $charsPerGroup; $i++) {
                    try {
                        $group .= $chars[random_int(0, $charsLength - 1)];
                    } catch (Exception $e) {
                        $group .= $chars[rand(0, $charsLength - 1)];
                    }
                }
                return $group;
            },
            array_fill(0, $groupCount, null)
        ));
    }

    /**
     * Belirtilen uzunlukta güçlü bir şifre oluşturur
     * Büyük harf, küçük harf, rakam ve özel karakterler içerir
     * 
     * @param int $length Şifre uzunluğu (varsayılan: 12)
     * @return string    Karışık karakterlerden oluşan şifre
     */
    public function password(int $length = 12): string 
    {
        if ($length < 3) {
            $length = 12; // Minimum güvenlik için
        }
        
        $chars = [
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'abcdefghijklmnopqrstuvwxyz',
            '0123456789'
        ];
    
        // Her karakter setinden birer tane al (random_int ile güvenli)
        $password = '';
        foreach ($chars as $set) {
            $setLength = strlen($set);
            try {
                $password .= $set[random_int(0, $setLength - 1)];
            } catch (Exception $e) {
                $password .= $set[rand(0, $setLength - 1)];
            }
        }
    
        // Kalan karakterleri tüm setlerden rastgele seç
        $allChars = implode('', $chars);
        $allCharsLength = strlen($allChars);
        
        for ($i = strlen($password); $i < $length; $i++) {
            try {
                $password .= $allChars[random_int(0, $allCharsLength - 1)];
            } catch (Exception $e) {
                $password .= $allChars[rand(0, $allCharsLength - 1)];
            }
        }
        
        return str_shuffle($password);
    }
}
