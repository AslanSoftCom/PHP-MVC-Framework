<?php

class Helpers {

    /**
     * 404 Not Found hata kodu gönderir, 404 hata sayfasını dahil eder ve işlemden çıkar.
     *
     * @return void
     */
    public function notFound(): void
    {
        http_response_code(404);
        include __DIR__ . '/../core/errors/404.php';
        exit;
    }

    /**
     * Belirtilen URL'ye yönlendirir ve isteğe bağlı olarak HTTP durum kodunu ayarlar.
     *
     * @param string $url Yönlendirilecek URL.
     * @param int $status HTTP durum kodu (varsayılan 302).
     * @return void
     */
    public function redirectToUrl(string $url, int $status = 302): void
    {
        http_response_code($status);
        header('Location: ' . $url);
        exit;
    }

    /**
     * Verilen veriyi JSON formatında çıktı olarak gönderir ve isteğe bağlı olarak HTTP durum kodunu ayarlar.
     *
     * @param mixed $data JSON olarak çıkışı yapılacak veri.
     * @param int $status HTTP durum kodu (varsayılan 200).
     * @return void
     */
    public function json($data, int $status = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
