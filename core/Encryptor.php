<?php

class Encryptor {

    /**
     * Şifreleme ve çözme işlemlerinde kullanılacak karakter seti.
     */
    private const CHARS = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    /**
     * Bir metni özel bir algoritma ile şifreler.
     *
     * @param string $text Şifrelenecek metin.
     * @return string Şifrelenmiş metin.
     */
    public function encode(string $text): string
    {
        $binaryString = array_reduce(str_split($text), fn($carry, $char) => $carry . str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT),'');
        $encodedString = '';
        $charsLength = strlen(self::CHARS);

        foreach (str_split($binaryString, 6) as $binaryChunk) {
            $index = bindec(str_pad($binaryChunk, 6, '0', STR_PAD_RIGHT));
            $encodedString .= self::CHARS[$index];
        }

        return $encodedString;
    }

    /**
     * Şifrelenmiş bir metni çözerek orijinal haline getirir.
     *
     * @param string $encodedText Çözülecek şifrelenmiş metin.
     * @return string Orijinal metin.
     */
    public function decode(string $encodedText): string
    {
        $binaryString = array_reduce(str_split($encodedText), fn($carry, $char) => $carry . str_pad(decbin(strpos(self::CHARS, $char)), 6, '0', STR_PAD_LEFT),'');
        $decodedText = '';
        foreach (str_split($binaryString, 8) as $byte) {
            if (strlen($byte) < 8) {
                continue;
            }
            $decodedText .= chr(bindec($byte));
        }

        return $decodedText;
    }
}
