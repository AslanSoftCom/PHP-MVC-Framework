<?php

class Controller {

    protected $db;
    protected $view;
    protected $request;
    protected $crypto;
    protected $device;
    protected $session;
    protected $cookie;
    protected $generate;
    protected $helpers;
    protected $upload;

    public function __construct() {
        $this->db = new DB();
        $this->view = new View();
        $this->request = new Request();
        $this->crypto = new Encryptor();
        $this->device = new Fingerprint();
        $this->session = new Session();
        $this->cookie = new Cookie();
        $this->generate = new Generate();
        $this->helpers = new Helpers();
        $this->upload = new Upload();
        $this->initialize();
        $this->loadModels();
    }

    public function initialize($cookieName = 'SID'): void {
        // Session zaten aktifse çık
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        
        session_name($cookieName);
        
        $cookie = $this->cookie->get($cookieName);
        $decodedData = $cookie ? $this->crypto->decode($cookie) : null;
        
        // Cookie verilerini doğrula
        if ($decodedData && mb_check_encoding($decodedData, 'UTF-8')) {
            $decodedParts = explode('|', $decodedData);
            $deviceData = [
                $this->device->ip(),
                $this->device->device(),
                $this->device->browser(),
                $this->device->language()
            ];
            
            // Session verilerinin device fingerprint ile eşleşip eşleşmediğini kontrol et
            if (count($decodedParts) >= 5 && array_slice($decodedParts, 1, 4) === $deviceData) {
                session_start();
                return;
            }
        }
        
        // Geçersiz veya yok olan cookie için yeni session oluştur
        $this->cookie->delete($cookieName);
        $deviceData = [
            $this->device->ip(),
            $this->device->device(),
            $this->device->browser(),
            $this->device->language()
        ];
        
        $sessionData = implode('|', [$this->generate->format(), ...$deviceData]);
        $encodedSessionId = $this->crypto->encode($sessionData);
        
        session_start();
        $this->cookie->set($cookieName, $encodedSessionId, 14400);
    }
    
    /**
     * Lazy loading ile modelleri yükler
     * Her istekte tüm modelleri yüklemek yerine, ihtiyaç duyulduğunda yüklenir
     */
    public function loadModels(): void {
        $modelFiles = glob("app/models/*.php");
        
        if (!$modelFiles) {
            return;
        }
        
        foreach ($modelFiles as $modelFile) {
            require_once $modelFile;
            $modelName = basename($modelFile, ".php");
            
            if (!class_exists($modelName)) {
                continue;
            }
            
            $propertyName = lcfirst($modelName);
            $this->{$propertyName} = new $modelName();
            
            // Modele controller bağımlılıklarını inject et
            $modelProperties = get_object_vars($this->{$propertyName});
            foreach ($modelProperties as $property => $value) {
                if (property_exists($this, $property) && isset($this->{$property})) {
                    $this->{$propertyName}->$property = $this->{$property};
                }
            }
        }
    }
}
