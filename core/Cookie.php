<?php

class Cookie {

    /**
     * Bir çerez oluşturur veya mevcut bir çerezi günceller.
     *
     * @param string $name Çerezin adı.
     * @param string $value Çereze atanacak değer.
     * @param int $expiry Çerezin geçerlilik süresi (saniye cinsinden). Varsayılan: 3600 saniye (1 saat).
     * @param string $path Çerezin geçerli olacağı yol. Varsayılan: '/'.
     * @param string $domain Çerezin geçerli olacağı alan adı. Varsayılan: boş (mevcut alan adı).
     * @param bool $secure Çerezin yalnızca HTTPS bağlantılarında kullanılabilir olup olmadığını belirtir. Varsayılan: `true`.
     * @param bool $httpOnly Çerezin JavaScript tarafından erişilebilir olup olmadığını belirtir. Varsayılan: `true`.
     * @return void
     */
    public static function set(string $name, string $value, int $expiry = 3600, string $path = '/', string $domain = '', bool $secure = true, bool $httpOnly = true) {
        setcookie($name, $value, [
            'expires' => time() + $expiry, // Çerezin süresi şu andan itibaren $expiry saniye sonra dolacak.
            'path' => $path,               // Çerezin geçerli olacağı yol.
            'domain' => $domain,           // Çerezin geçerli olacağı alan adı.
            'secure' => $secure,           // HTTPS bağlantılarında mı kullanılacak?
            'httponly' => $httpOnly,       // Çerez yalnızca HTTP protokolü üzerinden mi erişilebilir olacak?
            'samesite' => 'Strict'         // Çerezin "SameSite" politikası (Strict olarak ayarlandı).
        ]);
    }

    /**
     * Bir çerezin değerini alır.
     *
     * @param string $name Değeri alınacak çerezin adı.
     * @return string|null Çerezin değeri. Eğer çerez mevcut değilse `null` döner.
     */
    public static function get(string $name): ?string {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Bir çerezi siler.
     *
     * @param string $name Silinecek çerezin adı.
     * @param string $path Çerezin geçerli olduğu yol. Varsayılan: '/'.
     * @param string $domain Çerezin geçerli olduğu alan adı. Varsayılan: boş (mevcut alan adı).
     * @return void
     */
    public static function delete(string $name, string $path = '/', string $domain = '') {
        setcookie($name, '', [
            'expires' => time() - 3600, // Süresi geçmiş bir zaman belirlenir.
            'path' => $path,            // Çerezin geçerli olduğu yol.
            'domain' => $domain,        // Çerezin geçerli olduğu alan adı.
            'secure' => true,           // HTTPS bağlantılarında mı kullanılacak?
            'httponly' => true,         // Çerez yalnızca HTTP protokolü üzerinden mi erişilebilir olacak?
            'samesite' => 'Strict'      // Çerezin "SameSite" politikası (Strict olarak ayarlandı).
        ]);

        // $_COOKIE süper global dizisinden çerezi kaldırır.
        unset($_COOKIE[$name]);
    }
}
