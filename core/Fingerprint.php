<?php

class Fingerprint {

    /**
     * İstemcinin IP adresini döndürür.
     * Proxy ve load balancer header'larını güvenli bir şekilde kontrol eder.
     *
     * @return string IP adresi veya 'Unknown'.
     */
    public function ip(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwardedIps = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $firstIp = trim($forwardedIps[0]);
            
            // IP geçerliliğini kontrol et
            if (filter_var($firstIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $ip = $firstIp;
            }
        }
        
        return $ip;
    }

    /**
     * İstemcinin tercih ettiği dil tipini döndürür.
     *
     * @return string Dil tipi veya 'Unknown'.
     */
    public function language(): string
    {
        if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) || empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return 'Unknown';
        }
        
        $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $primaryLanguage = explode('-', $languages[0])[0];
        
        return trim($primaryLanguage);
    }

    /**
     * İstemcinin cihaz türünü (mobil, tablet, masaüstü) algılar ve döndürür.
     *
     * @return string Cihaz türü ('mobile', 'tablet', 'desktop') veya 'Unknown'.
     */
    public function device(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        foreach ([
            'mobile' => '/mobile|iphone|ipod|android.*mobile|windows.*phone|webos/i',
            'tablet' => '/tablet|ipad|playbook|silk|android(?!.*mobile)/i',
            'desktop' => '/windows nt|macintosh|linux/i'
        ] as $type => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $type;
            }
        }

        return 'Unknown';
    }

    /**
     * İstemcinin tarayıcı türünü algılar ve döndürür.
     *
     * @return string Tarayıcı türü ('edge', 'chrome', 'firefox', 'opera', 'internet Explorer', 'safari') veya 'Unknown'.
     */
    public function browser(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        foreach ([
            'edge' => '/edg/i',
            'chrome' => '/chrome|chromium|crios/i',
            'firefox' => '/firefox|fxios/i',
            'opera' => '/opera|opr/i',
            'internet Explorer' => '/msie|trident/i',
            'safari' => '/safari/i'
        ] as $browser => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $browser;
            }
        }

        return 'Unknown';
    }
}
