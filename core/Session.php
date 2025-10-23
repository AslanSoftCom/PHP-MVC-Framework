<?php

class Session {
    
    /**
     * Bir oturum (session) değişkeni oluşturur veya mevcut bir değişkeni günceller.
     *
     * @param string $key Oturum değişkeninin anahtarı (ismi).
     * @param mixed $value Oturum değişkenine atanacak değer.
     * @return void
     */
    public function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    /**
     * Bir oturum (session) değişkeninin değerini alır.
     *
     * @param string $key Değeri alınacak oturum değişkeninin anahtarı (ismi).
     * @return mixed Oturum değişkeninin değeri. Eğer anahtar mevcut değilse `null` döner.
     */
    public function get(string $key) {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Bir oturum (session) değişkenini siler.
     *
     * @param string $key Silinecek oturum değişkeninin anahtarı (ismi).
     * @return void
     */
    public function delete(string $key): void {
        unset($_SESSION[$key]);
    }
}
