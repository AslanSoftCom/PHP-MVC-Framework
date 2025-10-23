<?php

class ErrorHandler {
    
    private static $displayErrors = true;
    private static $errorTemplate = __DIR__ . '/../core/errors/500.php';

    /**
     * Hata yakalama mekanizmasını başlatır.
     * Hata ve istisna işleyicilerini ayarlar.
     *
     * @param bool $displayErrors Hataların görüntülenip görüntülenmeyeceğini belirler.
     */
    public static function init($displayErrors = true)
    {
        self::$displayErrors = $displayErrors;
        error_reporting(E_ALL);
        ob_start();
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * PHP tarafından yakalanan hataları işler.
     *
     * @param int $errno Hata numarası.
     * @param string $errstr Hata mesajı.
     * @param string $errfile Hatanın oluştuğu dosya.
     * @param int $errline Hatanın oluştuğu satır.
     */
    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        $severity = self::getErrorType($errno);
        $message = [
            'severity' => $severity,
            'message' => $errstr,
            'filepath' => $errfile,
            'line' => $errline,
            'backtrace' => debug_backtrace()
        ];
        self::displayError($message);
        exit;
    }

    /**
     * PHP tarafından yakalanan istisnaları işler.
     *
     * @param Throwable $exception Yakalanan istisna.
     */
    public static function handleException($exception)
    {
        $message = [
            'severity' => 'Exception: ' . get_class($exception),
            'message' => $exception->getMessage(),
            'filepath' => $exception->getFile(),
            'line' => $exception->getLine(),
            'backtrace' => $exception->getTrace()
        ];

        self::displayError($message);
        exit;
    }

    /**
     * PHP'nin kapanışında (shutdown) çalıştırılır.
     * Ölümcül hataları yakalar ve işler.
     */
    public static function handleShutdown()
    {
        $error = error_get_last();
        if ($error && ($error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR))) {
            $severity = self::getErrorType($error['type']);
            $message = [
                'severity' => "Fatal Error: $severity",
                'message' => $error['message'],
                'filepath' => $error['file'],
                'line' => $error['line'],
                'backtrace' => debug_backtrace()
            ];
            self::displayError($message);
            exit;
        }

        ob_end_flush();
    }

    /**
     * Hata mesajını uygun bir şekilde görüntüler.
     *
     * @param array $message Hata mesajı bilgileri.
     */
    private static function displayError($message)
    {
        ob_clean();
        if (self::$displayErrors) { 
            if (file_exists(self::$errorTemplate)) {
                extract($message);
                include self::$errorTemplate;
            } else {
                echo "Error template not found: " . self::$errorTemplate; 
            }
        }
    }

    /**
     * Hata numarasını hata türüne dönüştürür.
     *
     * @param int $errno Hata numarası.
     * @return string Hata türü.
     */
    private static function getErrorType($errno)
    {
        $errorTypes = [
            E_ERROR             => 'E_ERROR',
            E_WARNING           => 'E_WARNING',
            E_PARSE             => 'E_PARSE',
            E_NOTICE            => 'E_NOTICE',
            E_CORE_ERROR        => 'E_CORE_ERROR',
            E_CORE_WARNING      => 'E_CORE_WARNING',
            E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
            E_USER_ERROR        => 'E_USER_ERROR',
            E_USER_WARNING      => 'E_USER_WARNING',
            E_USER_NOTICE       => 'E_USER_NOTICE',
            E_STRICT            => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED        => 'E_DEPRECATED',
            E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
        ];

        return $errorTypes[$errno] ?? 'Unknown Error';
    }
}
