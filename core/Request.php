<?php

class Request {
    
    /**
     * HTTP isteğinin metodunu döndürür.
     * Eğer belirli bir metod belirtilmemişse varsayılan olarak 'GET' metodunu döner.
     *
     * @return string İstek metodu (GET, POST, vs.)
     */
    public function method(): string
    {
        return filter_input(INPUT_SERVER, 'REQUEST_METHOD') ?: 'GET';
    }

    /**
     * GET parametresinden belirtilen anahtara ait değeri güvenli bir şekilde döndürür.
     * Eğer anahtar mevcut değilse null döner.
     *
     * @param string $key Değerini almak istediğiniz GET parametresi anahtarı
     * @param bool $sanitize HTML karakterlerini encode etsin mi? (varsayılan: true)
     * @return string|array|null Parametre değeri veya null
     */
    public function get(string $key, bool $sanitize = true)
    {
        if (!isset($_GET[$key])) {
            return null;
        }
        
        $value = $_GET[$key];
        
        if (!$sanitize) {
            return $value;
        }
        
        // Array ise her elemanı sanitize et
        if (is_array($value)) {
            return array_map(function($item) {
                return is_string($item) ? htmlspecialchars($item, ENT_QUOTES, 'UTF-8') : $item;
            }, $value);
        }
        
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * POST parametresinden belirtilen anahtara ait değeri güvenli bir şekilde döndürür.
     * Eğer anahtar mevcut değilse null döner.
     *
     * @param string $key Değerini almak istediğiniz POST parametresi anahtarı
     * @param bool $sanitize HTML karakterlerini encode etsin mi? (varsayılan: true)
     * @return string|array|null Parametre değeri veya null
     */
    public function post(string $key, bool $sanitize = true)
    {
        if (!isset($_POST[$key])) {
            return null;
        }
        
        $value = $_POST[$key];
        
        if (!$sanitize) {
            return $value;
        }
        
        // Array ise her elemanı sanitize et
        if (is_array($value)) {
            return array_map(function($item) {
                return is_string($item) ? htmlspecialchars($item, ENT_QUOTES, 'UTF-8') : $item;
            }, $value);
        }
        
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Gelen dosya yüklemesinden belirtilen anahtara ait dosya bilgilerini döndürür.
     * Eğer anahtar mevcut değilse null döner.
     *
     * @param string $key Yüklenen dosyanın form anahtarı
     * @return array|null Dosya bilgileri veya null
     */
    public function getFile(string $key): ?array
    {
        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }
    
    /**
     * Çoklu dosya yüklemelerinde belirtilen anahtara ait tüm dosya bilgilerini döndürür.
     * Eğer anahtar mevcut değilse veya dosyalar çoklu değilse null döner.
     *
     * @param string $key Çoklu dosya yüklemesinin form anahtarı
     * @return array|null Dosyaların bilgileri dizisi veya null
     */
    public function getFileMultiple(string $key): ?array
    {
        if (!isset($_FILES[$key]) || !is_array($_FILES[$key]['name'])) {
            return null;
        }
        
        $files = [];
        foreach (array_keys($_FILES[$key]['name']) as $i) {
            $files[] = [
                'name' => $_FILES[$key]['name'][$i],
                'type' => $_FILES[$key]['type'][$i],
                'tmp_name' => $_FILES[$key]['tmp_name'][$i],
                'error' => $_FILES[$key]['error'][$i],
                'size' => $_FILES[$key]['size'][$i]
            ];
        }
        return $files;
    }
}
